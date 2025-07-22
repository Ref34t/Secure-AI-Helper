# Secure AI Helper

A WordPress plugin that provides AI-powered explanations of WordPress settings using OpenAI's GPT-4 API.

![WordPress Plugin](https://img.shields.io/badge/WordPress-Plugin-blue)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue)
![License](https://img.shields.io/badge/License-GPL%20v2-green)

## 🚀 Features

- **AI-Powered Explanations** - Get clear, helpful explanations for any WordPress setting
- **OpenAI GPT-4 Integration** - Uses the latest GPT-4 model for accurate responses
- **Clean Admin Interface** - Easy-to-use WordPress admin panel
- **Secure API Key Storage** - Safe handling of your OpenAI API keys
- **YAML Configuration** - Customizable AI behavior and constraints
- **WordPress Security** - Follows WordPress security best practices

## 📦 Installation

### Method 1: Upload Plugin ZIP

1. Download the latest release ZIP file
2. Go to **WordPress Admin** → **Plugins** → **Add New** → **Upload Plugin**
3. Select the ZIP file and click **Install Now**
4. Click **Activate Plugin**

### Method 2: Manual Installation

1. Download or clone this repository
2. Upload the `secure-ai-helper` folder to `/wp-content/plugins/`
3. Activate the plugin through the WordPress **Plugins** menu

## ⚙️ Configuration

1. **Get OpenAI API Key**
   - Visit [OpenAI Platform](https://platform.openai.com/)
   - Create an account and generate an API key

2. **Configure Plugin**
   - Go to **WordPress Admin** → **AI Helper**
   - Enter your OpenAI API key
   - Click **Save Changes**

3. **Test the Plugin**
   - Enter a WordPress setting name (e.g., `blog_name`, `users_can_register`)
   - Click **Get Explanation**
   - View the AI-generated explanation

## 🎯 Usage Examples

Try asking about these WordPress settings:

- `blog_name` - Your site title
- `users_can_register` - User registration setting
- `default_role` - Default user role for new registrations
- `start_of_week` - First day of the week
- `date_format` - Date format setting
- `permalink_structure` - URL structure setting

## 🛠️ Development

### Requirements

- PHP 7.4 or higher
- WordPress 5.0 or higher
- Composer (for development)

### Setup

```bash
git clone https://github.com/Ref34t/Secure-AI-Helper.git
cd Secure-AI-Helper
composer install
```

### File Structure

```
secure-ai-helper/
├── assets/
│   ├── ai-helper.js      # Frontend JavaScript
│   └── style.css         # Admin styles
├── config/
│   └── ai-config.yaml    # AI configuration
├── includes/
│   ├── class-admin-page.php    # Admin interface
│   └── class-openai-client.php # OpenAI API client
├── vendor/               # Composer dependencies
├── secure-ai-helper.php  # Main plugin file
└── README.txt           # WordPress plugin readme
```

### Key Components

- **SAI_Admin_Page** - Handles WordPress admin interface and AJAX requests
- **SAI_OpenAI_Client** - Manages OpenAI API communication
- **YAML Configuration** - Defines AI behavior and constraints

## 🔒 Security

This plugin implements WordPress security best practices:

- **Nonce Verification** - All AJAX requests are protected
- **Capability Checks** - Only administrators can access settings
- **Input Sanitization** - All user inputs are properly sanitized
- **API Key Protection** - Secure handling of sensitive API keys

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the GPL v2 License - see the [LICENSE](LICENSE) file for details.

## 🐛 Issues & Support

If you encounter any issues or need support:

1. Check existing [GitHub Issues](https://github.com/Ref34t/Secure-AI-Helper/issues)
2. Create a new issue with detailed information
3. Include WordPress version, PHP version, and error messages

## 📝 Changelog

### Version 1.0.0
- Initial release
- OpenAI GPT-4 integration
- WordPress admin interface
- YAML-based configuration
- Security implementation

## ⚡ Performance Notes

- API requests are cached to improve performance
- Timeouts are set to prevent hanging requests
- Response limits prevent excessive token usage

## 🌟 Acknowledgments

- Built with [Symfony YAML](https://symfony.com/doc/current/components/yaml.html)
- Powered by [OpenAI GPT-4](https://openai.com/)
- Follows [WordPress Plugin Standards](https://developer.wordpress.org/plugins/)

---

Made with ❤️ for the WordPress community