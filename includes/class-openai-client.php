<?php
use Symfony\Component\Yaml\Yaml;

class SAI_OpenAI_Client {
    protected $config;

    public function __construct() {
        $yaml_path = plugin_dir_path(__FILE__) . '../config/ai-config.yaml';
        
        if (!file_exists($yaml_path)) {
            $this->log_error('Configuration file not found', $yaml_path);
            $this->config = $this->get_default_config();
            return;
        }

        try {
            $this->config = Yaml::parseFile($yaml_path);
            $this->validate_config();
        } catch (Exception $e) {
            $this->log_error('YAML parsing failed', $e->getMessage());
            $this->config = $this->get_default_config();
        }
    }

    public function explain($setting) {
        $api_key = $this->get_encrypted_api_key();
        if (!$api_key) return 'API key is not set.';

        if (!$this->validate_input($setting)) {
            return 'Invalid input. Please provide a valid WordPress setting name.';
        }

        if (!$this->check_rate_limit()) {
            return 'Too many requests. Please wait before trying again.';
        }

        $context = $this->config['context'] ?? '';
        $constraints = implode(', ', $this->config['constraints'] ?? []);
        $setting = sanitize_text_field($setting);

        $prompt = "{$context}\nConstraints: {$constraints}\nExplain the WordPress setting: {$setting}";

        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type'  => 'application/json',
            ],
            'body' => json_encode([
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => $context],
                    ['role' => 'user', 'content' => "Explain this WordPress setting: {$setting} (Constraints: {$constraints})"]
                ],
                'temperature' => 0.7,
            ]),
        ]);

        if (is_wp_error($response)) {
            $this->log_error('OpenAI API request failed', $response->get_error_message());
            return 'Unable to connect to AI service. Please try again later.';
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (!isset($body['choices'][0]['message']['content'])) {
            $this->log_error('Invalid OpenAI response', wp_remote_retrieve_body($response));
            return 'Invalid response from AI service. Please try again.';
        }

        $this->update_rate_limit();
        return $body['choices'][0]['message']['content'];
    }

    private function get_encrypted_api_key() {
        $encrypted_key = get_option('sai_openai_key');
        error_log('SAI Debug: Retrieved option value: ' . ($encrypted_key ? 'Set (' . strlen($encrypted_key) . ' chars)' : 'Empty'));
        
        if (!$encrypted_key) return false;
        
        $decrypted = $this->decrypt_api_key($encrypted_key);
        error_log('SAI Debug: Decrypted key: ' . ($decrypted ? 'Success (' . strlen($decrypted) . ' chars)' : 'Failed'));
        
        return $decrypted;
    }

    private function decrypt_api_key($encrypted_key) {
        error_log('SAI Debug: Decrypting key, length: ' . strlen($encrypted_key));
        
        if (!function_exists('openssl_decrypt')) {
            error_log('SAI Debug: OpenSSL decrypt not available, trying base64');
            $decoded = base64_decode($encrypted_key, true);
            return $decoded !== false ? $decoded : $encrypted_key;
        }
        
        $salt = wp_salt('secure_auth');
        $key = substr(hash('sha256', $salt), 0, 32);
        $iv_length = openssl_cipher_iv_length('AES-256-CBC');
        
        error_log('SAI Debug: IV length: ' . $iv_length . ', encrypted key length: ' . strlen($encrypted_key));
        
        // If string is too short, might be base64 encoded
        if (strlen($encrypted_key) < $iv_length) {
            error_log('SAI Debug: Key too short for OpenSSL, trying base64');
            $decoded = base64_decode($encrypted_key, true);
            return $decoded !== false ? $decoded : $encrypted_key;
        }
        
        $iv = substr($encrypted_key, 0, $iv_length);
        $encrypted_data = substr($encrypted_key, $iv_length);
        
        error_log('SAI Debug: Attempting OpenSSL decryption');
        $decrypted = openssl_decrypt($encrypted_data, 'AES-256-CBC', $key, 0, $iv);
        
        if ($decrypted === false) {
            error_log('SAI Debug: OpenSSL decryption failed, trying base64 fallback');
            $decoded = base64_decode($encrypted_key, true);
            return $decoded !== false ? $decoded : $encrypted_key;
        }
        
        error_log('SAI Debug: OpenSSL decryption successful');
        return $decrypted;
    }

    public static function encrypt_api_key($api_key) {
        error_log('SAI Debug: Encrypting API key, length: ' . strlen($api_key));
        
        if (!function_exists('openssl_encrypt')) {
            error_log('SAI Debug: OpenSSL not available, storing as base64');
            return base64_encode($api_key);
        }
        
        $salt = wp_salt('secure_auth');
        $key = substr(hash('sha256', $salt), 0, 32);
        
        // Use modern random_bytes instead of deprecated openssl_random_pseudo_bytes
        $iv_length = openssl_cipher_iv_length('AES-256-CBC');
        if (function_exists('random_bytes')) {
            $iv = random_bytes($iv_length);
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $iv = openssl_random_pseudo_bytes($iv_length);
        } else {
            error_log('SAI Debug: No secure random function available, using base64');
            return base64_encode($api_key);
        }
        
        $encrypted = openssl_encrypt($api_key, 'AES-256-CBC', $key, 0, $iv);
        if ($encrypted === false) {
            error_log('SAI Debug: Encryption failed, storing as base64');
            return base64_encode($api_key);
        }
        
        error_log('SAI Debug: Encryption successful');
        return $iv . $encrypted;
    }

    private function validate_input($setting) {
        if (empty($setting) || !is_string($setting)) return false;
        if (strlen($setting) > 200) return false;
        if (preg_match('/[<>"\']/', $setting)) return false;
        return true;
    }

    private function check_rate_limit() {
        $transient_key = 'sai_rate_limit_' . get_current_user_id();
        $requests = get_transient($transient_key) ?: 0;
        return $requests < 10;
    }

    private function update_rate_limit() {
        $transient_key = 'sai_rate_limit_' . get_current_user_id();
        $requests = get_transient($transient_key) ?: 0;
        set_transient($transient_key, $requests + 1, HOUR_IN_SECONDS);
    }

    private function validate_config() {
        if (!is_array($this->config)) {
            throw new Exception('Invalid configuration format');
        }

        if (empty($this->config['context'])) {
            $this->log_error('Missing context in configuration');
        }

        if (!isset($this->config['constraints']) || !is_array($this->config['constraints'])) {
            $this->log_error('Missing or invalid constraints in configuration');
        }
    }

    private function get_default_config() {
        return [
            'context' => 'You are a helpful WordPress assistant.',
            'constraints' => [
                'Be helpful and accurate',
                'Focus on WordPress settings only',
                'Avoid recommending unsafe changes'
            ]
        ];
    }

    private function log_error($message, $details = '') {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("SAI Error: {$message} - {$details}");
        }
    }
}
