<?php
defined('ABSPATH') || exit;

class SAI_Admin_Page {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_ajax_sai_get_ai_help', [$this, 'handle_ajax']);
    }

    public function add_menu() {
        add_menu_page('Secure AI Helper', 'AI Helper', 'manage_options', 'sai-helper', [$this, 'render_page']);
    }

    public function register_settings() {
        register_setting('sai_settings_group', 'sai_openai_key', [
            'sanitize_callback' => [$this, 'sanitize_api_key']
        ]);
    }

    public function sanitize_api_key($input) {
        // Debug logging
        error_log('SAI Debug: sanitize_api_key called with input length: ' . strlen($input));
        
        if (empty($input)) {
            error_log('SAI Debug: Empty input, returning existing key');
            return get_option('sai_openai_key');
        }
        
        $clean_input = sanitize_text_field($input);
        error_log('SAI Debug: Clean input length: ' . strlen($clean_input));
        
        if (strlen($clean_input) < 40 || !preg_match('/^sk-[a-zA-Z0-9_-]+$/', $clean_input)) {
            add_settings_error('sai_openai_key', 'invalid-key', 'Please enter a valid OpenAI API key (starts with sk- and at least 40 characters).');
            error_log('SAI Debug: Invalid key format');
            return get_option('sai_openai_key');
        }
        
        $encrypted = SAI_OpenAI_Client::encrypt_api_key($clean_input);
        error_log('SAI Debug: Key encrypted successfully, length: ' . strlen($encrypted));
        add_settings_error('sai_openai_key', 'key-saved', 'API key saved and encrypted successfully!', 'success');
        
        return $encrypted;
    }

    public function enqueue_assets() {
        wp_enqueue_script('sai-helper', plugin_dir_url(__FILE__) . '../assets/ai-helper.js', ['jquery'], null, true);
        wp_localize_script('sai-helper', 'sai_ajax', ['ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('sai_nonce')]);
        wp_enqueue_style('sai-style', plugin_dir_url(__FILE__) . '../assets/style.css');
    }

    public function render_page() {
        ?>
        <div class="wrap">
            <h1>Secure AI Helper Settings</h1>
            <?php settings_errors('sai_openai_key'); ?>
            <form method="post" action="options.php">
                <?php
                settings_fields('sai_settings_group');
                do_settings_sections('sai_settings_group');
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">OpenAI API Key</th>
                        <td>
                            <input type="password" name="sai_openai_key" value="" placeholder="<?php echo get_option('sai_openai_key') ? 'API key is set (encrypted)' : 'Enter your OpenAI API key'; ?>" style="width: 400px;" />
                            <?php if (get_option('sai_openai_key')): ?>
                                <p class="description">API key is currently saved and encrypted. Enter a new key to replace it.</p>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
            <hr>
            <h2>Explain a WordPress Setting</h2>
            <input type="text" id="sai-setting-name" placeholder="Enter setting name">
            <button id="sai-explain-btn" class="button button-primary">Explain</button>
            <pre id="sai-output"></pre>
        </div>
        <?php
    }

    public function handle_ajax() {
        check_ajax_referer('sai_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Permission denied');
        $setting = sanitize_text_field($_POST['setting']);
        $client = new SAI_OpenAI_Client();
        $response = $client->explain($setting);
        wp_send_json_success($response);
    }
}
