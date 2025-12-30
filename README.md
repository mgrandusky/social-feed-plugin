# Social Feed Plugin

A comprehensive WordPress plugin that aggregates social media feeds from Facebook, Instagram, Twitter, and LinkedIn into a single, customizable display block.

## Features

- **Multi-Platform Support**: Integrates with Facebook, Instagram, Twitter (X), and LinkedIn
- **Easy Configuration**: Admin interface for managing API keys and display preferences
- **Shortcode Integration**: Simple `[social_feeds]` shortcode for embedding feeds anywhere
- **Smart Caching**: Reduces API calls and improves load times with configurable cache duration
- **Responsive Design**: Mobile-friendly grid layout that adapts to any screen size
- **Chronological Display**: Shows posts from all platforms in chronological order
- **Media Support**: Displays images and videos from all supported platforms
- **Error Handling**: Graceful handling of API failures and rate limits
- **Security First**: Follows WordPress security best practices with proper sanitization and nonce validation
- **Localization Ready**: Full support for translations with included POT file

## Installation

1. Upload the `social-feed-plugin` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Social Feeds to configure your API credentials
4. Add the `[social_feeds]` shortcode to any post or page

## Configuration

### Facebook Setup

1. Go to [Facebook Developers](https://developers.facebook.com)
2. Create a new app and get your App ID and App Secret
3. Add the Facebook Page ID you want to display
4. (Optional) Generate a Page Access Token for enhanced functionality

### Instagram Setup

1. Use Instagram Basic Display API or Instagram Graph API
2. Create an app at [Facebook Developers](https://developers.facebook.com)
3. Generate a long-lived access token
4. Enter the access token in the plugin settings

### Twitter Setup

1. Go to [Twitter Developer Portal](https://developer.twitter.com)
2. Create a new project and app
3. Generate a Bearer Token with read permissions
4. Enter your Twitter username (without @) and bearer token

### LinkedIn Setup

1. Go to [LinkedIn Developer Platform](https://developer.linkedin.com)
2. Create a new app
3. Request access to required API products
4. Generate an access token with proper scopes (r_liteprofile, w_member_social)

## Usage

### Basic Shortcode

```
[social_feeds]
```

### Shortcode with Parameters

```
[social_feeds limit="20" platforms="facebook,twitter"]
```

**Parameters:**
- `limit` - Number of posts to display (default: from settings)
- `platforms` - Comma-separated list of platforms to show (default: all enabled)

## File Structure

```
social-feed-plugin/
├── admin/
│   ├── class-social-feed-admin.php
│   └── partials/
│       └── admin-display.php
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── public.css
│   └── js/
│       ├── admin.js
│       └── public.js
├── includes/
│   ├── class-social-feed-activator.php
│   ├── class-social-feed-api-base.php
│   ├── class-social-feed-cache.php
│   ├── class-social-feed-deactivator.php
│   ├── class-social-feed-facebook-api.php
│   ├── class-social-feed-i18n.php
│   ├── class-social-feed-instagram-api.php
│   ├── class-social-feed-linkedin-api.php
│   ├── class-social-feed-loader.php
│   ├── class-social-feed-plugin.php
│   └── class-social-feed-twitter-api.php
├── languages/
│   └── social-feed-plugin.pot
├── public/
│   ├── class-social-feed-public.php
│   └── partials/
│       └── feed-display.php
├── social-feed-plugin.php
├── uninstall.php
└── README.md
```

## Security Features

- Input sanitization using WordPress functions
- Output escaping for XSS prevention
- Nonce validation for form submissions
- Capability checks for admin access
- Secure cache directory with .htaccess protection
- Safe API credential storage

## API Rate Limits

The plugin includes caching to minimize API requests:
- Default cache duration: 1 hour (configurable)
- Automatic cache clearing on settings update
- Manual cache clear option in admin interface

## Error Handling

- Graceful degradation when platforms are unavailable
- User-friendly error messages
- Automatic retry with cached data
- Admin notifications for configuration issues

## Customization

### Custom CSS

Add custom styles by targeting these classes:
- `.social-feed-container` - Main container
- `.social-feed-grid` - Grid layout
- `.social-feed-item` - Individual post
- `.social-feed-facebook` - Facebook posts
- `.social-feed-instagram` - Instagram posts
- `.social-feed-twitter` - Twitter posts
- `.social-feed-linkedin` - LinkedIn posts

### Filters and Hooks

The plugin is designed with extensibility in mind and follows WordPress plugin development best practices.

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- API credentials from each social media platform you want to use

## License

GPL-2.0+

## Support

For issues and feature requests, please use the [GitHub issue tracker](https://github.com/mgrandusky/social-feed-plugin/issues).

## Credits

Developed with ❤️ following WordPress coding standards and best practices.
