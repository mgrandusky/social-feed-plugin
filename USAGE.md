# Social Feed Plugin - Usage Guide

## Overview

The Social Feed Plugin is a comprehensive WordPress solution for aggregating social media content from multiple platforms into a unified display on your WordPress site.

## Quick Start

1. **Install and Activate**
   - Upload the plugin to `/wp-content/plugins/`
   - Activate via WordPress admin panel

2. **Configure API Keys**
   - Navigate to Settings > Social Feeds
   - Enable desired platforms
   - Enter API credentials for each platform

3. **Add to Your Site**
   - Use shortcode: `[social_feeds]`
   - Add to posts, pages, or widgets

## Detailed Configuration

### Facebook Configuration

**Prerequisites:**
- Facebook Page (not personal profile)
- Facebook Developer account

**Steps:**
1. Visit [Facebook Developers](https://developers.facebook.com)
2. Create a new app (or use existing)
3. Add the Facebook Page product
4. Get your App ID and App Secret
5. Find your Page ID (from your Page settings or URL)
6. (Optional) Generate a long-lived Page Access Token

**Plugin Settings:**
- Enable Facebook: ✓
- Facebook App ID: Your app ID
- Facebook App Secret: Your app secret
- Facebook Access Token: (Optional) Your page access token
- Facebook Page ID: Your page ID

### Instagram Configuration

**Prerequisites:**
- Instagram Business or Creator account
- Facebook Page connected to Instagram

**Steps:**
1. Visit [Facebook Developers](https://developers.facebook.com)
2. Create an app and add Instagram Basic Display or Instagram Graph API
3. Generate a long-lived User Access Token
4. Token should have permissions: `instagram_basic` or `instagram_graph_user_profile`

**Plugin Settings:**
- Enable Instagram: ✓
- Instagram Access Token: Your long-lived access token

**Token Refresh:**
Instagram tokens expire. For Instagram Basic Display:
- Short-lived tokens: 1 hour
- Long-lived tokens: 60 days (can be refreshed)

### Twitter (X) Configuration

**Prerequisites:**
- Twitter/X account
- Twitter Developer account

**Steps:**
1. Visit [Twitter Developer Portal](https://developer.twitter.com)
2. Create a new project and app
3. Set app permissions to "Read Only"
4. Generate a Bearer Token
5. Copy your Bearer Token

**Plugin Settings:**
- Enable Twitter: ✓
- Twitter Bearer Token: Your bearer token
- Twitter Username: Your username (without @)

**API Versions:**
- Plugin uses Twitter API v2
- Free tier: 1,500 tweets per month
- Basic tier: 10,000 tweets per month

### LinkedIn Configuration

**Prerequisites:**
- LinkedIn personal or company page
- LinkedIn Developer account

**Steps:**
1. Visit [LinkedIn Developers](https://developer.linkedin.com)
2. Create a new app
3. Request access to "Share on LinkedIn" and "Sign In with LinkedIn"
4. Implement OAuth 2.0 flow to get access token
5. Required scopes: `r_liteprofile`, `w_member_social`

**Plugin Settings:**
- Enable LinkedIn: ✓
- LinkedIn Access Token: Your OAuth access token

**Note:** LinkedIn tokens expire after 60 days and require refresh.

## Shortcode Usage

### Basic Usage

Display all enabled platforms with default settings:
```
[social_feeds]
```

### Advanced Usage

**Limit number of posts:**
```
[social_feeds limit="15"]
```

**Show specific platforms only:**
```
[social_feeds platforms="facebook,instagram"]
```

**Combine parameters:**
```
[social_feeds limit="20" platforms="twitter,linkedin"]
```

### Shortcode Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `limit` | integer | 10 | Number of posts to display |
| `platforms` | string | all enabled | Comma-separated platform list |

**Valid platform values:**
- facebook
- instagram
- twitter
- linkedin

## Cache Management

### Automatic Caching

- Default duration: 1 hour (3600 seconds)
- Configurable in Settings > Social Feeds
- Reduces API calls and improves performance

### Manual Cache Clear

1. Go to Settings > Social Feeds
2. Scroll to "Cache Management"
3. Click "Clear Cache"
4. Feeds will refresh on next page load

### Cache Location

- Directory: `/wp-content/uploads/social-feed-cache/`
- Protected with `.htaccess`
- Automatically created on activation
- Cleaned on uninstall

## Customization

### CSS Customization

Add custom styles to your theme's CSS:

```css
/* Change grid columns */
.social-feed-grid {
    grid-template-columns: repeat(4, 1fr);
}

/* Customize Facebook posts */
.social-feed-facebook {
    background-color: #f0f2f5;
}

/* Style post headers */
.social-feed-header {
    background: linear-gradient(to right, #667eea, #764ba2);
    color: white;
}

/* Adjust image display */
.social-feed-media img {
    border-radius: 8px;
}
```

### Template Override

Copy templates to your theme:
```
your-theme/social-feed-plugin/feed-display.php
```

The plugin will use your theme template if found.

### Available CSS Classes

**Container:**
- `.social-feed-container` - Main wrapper
- `.social-feed-grid` - Grid layout

**Items:**
- `.social-feed-item` - Individual post
- `.social-feed-facebook` - Facebook specific
- `.social-feed-instagram` - Instagram specific
- `.social-feed-twitter` - Twitter specific
- `.social-feed-linkedin` - LinkedIn specific

**Components:**
- `.social-feed-header` - Post header
- `.social-feed-media` - Media container
- `.social-feed-content` - Post text
- `.social-feed-footer` - Post footer
- `.social-feed-link` - External link

## Error Handling

### Common Issues

**"API is not configured"**
- Enable the platform in settings
- Enter valid API credentials
- Save settings

**"No posts available"**
- Check if accounts have public posts
- Verify API credentials are correct
- Clear cache and try again

**"Rate limit exceeded"**
- Wait before next API call
- Increase cache duration
- Check API plan limits

**"Authentication failed"**
- Regenerate access token
- Check token permissions/scopes
- Verify token hasn't expired

### API Rate Limits

**Facebook:**
- 200 calls per hour per user
- Use page access token for better limits

**Instagram:**
- 200 calls per hour per user
- Rate limit applies to all apps

**Twitter:**
- Free: 1,500 tweets/month, 500,000 tweets read/month
- Basic: 10,000 tweets/month, 10,000,000 tweets read/month

**LinkedIn:**
- Varies by API product
- Typically 100 requests per day for free tier

## Security Best Practices

### Protecting API Keys

1. Never commit keys to version control
2. Use environment variables when possible
3. Rotate tokens regularly
4. Use minimal required permissions

### WordPress Security

The plugin implements:
- Input sanitization
- Output escaping
- Nonce validation
- Capability checks
- Secure file permissions

### Privacy Considerations

- Only displays public posts
- No personal data collection
- Complies with platform ToS
- Cache stored securely

## Troubleshooting

### Debug Mode

Enable WordPress debug mode in `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

Check `/wp-content/debug.log` for errors.

### Browser Console

Open browser developer tools and check for:
- JavaScript errors
- Failed network requests
- CSS loading issues

### Test Individual Platforms

Use shortcode to test one platform:
```
[social_feeds platforms="facebook" limit="5"]
```

### Verify File Permissions

Cache directory should be writable:
```bash
chmod 755 /wp-content/uploads/social-feed-cache/
```

## Performance Optimization

### Recommended Settings

- Cache Duration: 3600-7200 seconds (1-2 hours)
- Posts Per Page: 10-20
- Use specific platforms only when possible

### Reduce Load Time

1. Increase cache duration
2. Limit number of posts
3. Use lazy loading for images
4. Enable browser caching
5. Use CDN for static assets

### Server Requirements

- PHP 7.2+ (7.4+ recommended)
- WordPress 5.0+
- cURL or allow_url_fopen enabled
- Write permissions to uploads directory

## Frequently Asked Questions

**Q: Can I display posts from multiple accounts?**
A: Currently, each platform shows posts from one configured account.

**Q: Do I need all four platform API keys?**
A: No, enable only the platforms you want to use.

**Q: Will this slow down my site?**
A: No, the caching system prevents slowdowns. First load may be slower.

**Q: Can I customize the design?**
A: Yes, use custom CSS or override the template file.

**Q: What happens when a token expires?**
A: The plugin will show an error. Regenerate and update the token.

**Q: Is the plugin GDPR compliant?**
A: Yes, it only displays public data and doesn't collect user information.

**Q: Can I use it in widgets?**
A: Yes, use the shortcode in a text/HTML widget.

**Q: Does it work with page builders?**
A: Yes, add the shortcode to your page builder's HTML/code block.

## Support and Resources

- **Documentation:** [GitHub Repository](https://github.com/mgrandusky/social-feed-plugin)
- **Issues:** [GitHub Issues](https://github.com/mgrandusky/social-feed-plugin/issues)
- **WordPress Plugin Directory:** Coming soon

## Version History

### 1.0.0 (Initial Release)
- Multi-platform support (Facebook, Instagram, Twitter, LinkedIn)
- Configurable admin interface
- Caching system
- Shortcode functionality
- Responsive design
- Localization support
- Security implementations

## Contributing

Contributions are welcome! Please:
1. Fork the repository
2. Create a feature branch
3. Follow WordPress coding standards
4. Submit a pull request

## License

GPL-2.0+ - See LICENSE file for details.
