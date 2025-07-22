=== Secure AI Helper ===
Contributors: mohamedkhaled
Tags: ai, openai, wordpress, settings, help
Requires at least: 5.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPL v2 or later

Securely explains WordPress settings using OpenAI with enhanced security features.

== Description ==

Secure AI Helper is a WordPress plugin that provides AI-powered explanations of WordPress settings using OpenAI's API. The plugin includes enterprise-level security features:

* **API Key Encryption** - OpenAI API keys are encrypted using AES-256-CBC
* **Rate Limiting** - 10 requests per hour per user to prevent abuse
* **Input Validation** - XSS protection and input sanitization
* **Error Handling** - Comprehensive logging and graceful error recovery
* **YAML Configuration** - Flexible AI behavior configuration

== Installation ==

1. Upload the plugin ZIP file through WordPress Admin → Plugins → Add New → Upload Plugin
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to 'AI Helper' in the admin menu to configure your OpenAI API key

All dependencies are included in the plugin package - no additional setup required!

== Configuration ==

1. Get an OpenAI API key from https://platform.openai.com/
2. Enter your API key in the plugin settings (it will be automatically encrypted)
3. Modify `config/ai-config.yaml` to customize AI behavior if needed

== Security Features ==

* API keys are encrypted with WordPress salts
* Rate limiting prevents API abuse
* Input validation prevents XSS attacks
* Comprehensive error logging
* WordPress security best practices

== Changelog ==

= 1.1.0 =
* Added API key encryption
* Implemented rate limiting
* Enhanced input validation
* Improved error handling
* Added YAML configuration validation
* Frontend security improvements