<?php
/**
 * Plugin Name: Secure AI Helper
 * Description: Securely explains WordPress settings using OpenAI with YAML-based context.
 * Version: 1.1.0
 * Author: Mohamed Khaled
 */

defined('ABSPATH') || exit;

require_once __DIR__ . '/vendor/autoload.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-admin-page.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-openai-client.php';

add_action('init', function() {
    if (is_admin()) {
        new SAI_Admin_Page();
    }
});
