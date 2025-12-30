# Social Feed Plugin - Developer Notes

## Architecture Overview

### Plugin Structure

The plugin follows a modular, object-oriented architecture that adheres to WordPress plugin development best practices.

```
Main Plugin File
    ├── Activator/Deactivator
    ├── Core Plugin Class
    │   ├── Loader (Hooks/Filters)
    │   ├── i18n (Localization)
    │   ├── Admin Class
    │   │   ├── Settings Page
    │   │   ├── Field Validation
    │   │   └── Assets (CSS/JS)
    │   └── Public Class
    │       ├── Shortcode Handler
    │       ├── Feed Aggregator
    │       └── Assets (CSS/JS)
    └── API Layer
        ├── Base API Class
        ├── Facebook API
        ├── Instagram API
        ├── Twitter API
        └── LinkedIn API
```

### Design Patterns

1. **Abstract Base Class Pattern**
   - `Social_Feed_API_Base` provides common functionality
   - Platform-specific classes extend the base
   - Promotes code reuse and consistency

2. **Dependency Injection**
   - Components receive dependencies through constructor
   - Improves testability and flexibility

3. **Hook System**
   - Loader class manages all WordPress hooks
   - Centralized hook registration
   - Easy to track and modify

4. **Template Pattern**
   - Separate template files for display
   - Theme override support
   - Clean separation of logic and presentation

## Security Measures

### Input Validation
- All user input sanitized using WordPress functions
- `sanitize_text_field()` for text inputs
- `sanitize_textarea_field()` for multi-line text
- `absint()` for integers
- `esc_url_raw()` for URLs

### Output Escaping
- `esc_html()` for HTML content
- `esc_attr()` for HTML attributes
- `esc_url()` for URLs
- `wp_kses_post()` for allowed HTML

### Authentication & Authorization
- `current_user_can()` checks capabilities
- `check_admin_referer()` validates nonces
- Settings page requires `manage_options` capability

### Data Protection
- API credentials stored in WordPress options table
- Cache directory protected with `.htaccess`
- No sensitive data in JavaScript
- Proper file permissions

### API Security
- HTTPS-only API calls
- Error messages don't expose sensitive data
- Rate limiting through caching
- Credential validation before API calls

## API Integration Details

### Facebook Graph API
- **Version:** v18.0 (configurable via constant)
- **Authentication:** App ID + Secret or Access Token
- **Endpoints:** `/page-id/posts`
- **Fields:** id, message, created_time, full_picture, type, permalink_url
- **Rate Limits:** 200 calls/hour per user

### Instagram Graph API
- **Authentication:** Long-lived User Access Token
- **Endpoints:** `/me/media`
- **Fields:** id, caption, media_type, media_url, permalink, thumbnail_url, timestamp
- **Token Expiry:** 60 days (requires refresh)

### Twitter API v2
- **Authentication:** Bearer Token
- **Endpoints:** `/users/:id/tweets`
- **Fields:** created_at, entities, attachments
- **Rate Limits:** Varies by tier (500K reads/month free)

### LinkedIn API
- **Authentication:** OAuth 2.0 Access Token
- **Endpoints:** `/v2/shares`
- **Scopes:** r_liteprofile, w_member_social
- **Token Expiry:** 60 days

## Caching System

### Implementation
- File-based caching in `/wp-content/uploads/social-feed-cache/`
- MD5 hash of cache key as filename
- Serialized data storage
- Automatic expiration check

### Cache Keys
- Format: `social_feed_{platform}`
- Examples:
  - `social_feed_facebook`
  - `social_feed_instagram`
  - `social_feed_twitter`
  - `social_feed_linkedin`

### Cache Duration
- Configurable in admin settings
- Default: 3600 seconds (1 hour)
- Prevents API rate limit issues
- Improves page load performance

## Data Flow

### Feed Retrieval Flow
1. User visits page with `[social_feeds]` shortcode
2. Public class processes shortcode
3. For each enabled platform:
   - Check if cached data exists and is valid
   - If cached: return cached data
   - If not cached:
     - Make API request
     - Normalize data to standard format
     - Cache the response
     - Return normalized data
4. Merge all platform data
5. Sort by timestamp (chronological order)
6. Limit to configured number of posts
7. Render using template

### Normalized Data Format
```php
array(
    'id' => string,           // Platform-specific post ID
    'platform' => string,     // 'facebook', 'instagram', 'twitter', 'linkedin'
    'text' => string,         // Post content/caption
    'created_time' => int,    // Unix timestamp
    'image' => string,        // Image URL or empty
    'video' => string,        // Video URL or empty
    'link' => string,         // Link to original post
    'type' => string,         // Post type (photo, video, text, etc.)
)
```

## Error Handling

### Error Types
1. **Configuration Errors**
   - Missing API credentials
   - Invalid credentials
   - Missing required fields

2. **API Errors**
   - Connection failures
   - Authentication failures
   - Rate limit exceeded
   - Invalid response format

3. **System Errors**
   - Cache directory not writable
   - PHP errors

### Error Display
- Errors shown only to users with manage_options capability
- User-friendly error messages in frontend
- Detailed errors in WordPress debug log
- Platform-specific error handling

## WordPress Standards Compliance

### Coding Standards
- WordPress PHP Coding Standards
- Proper indentation (tabs for indentation, spaces for alignment)
- Descriptive variable and function names
- Comprehensive inline documentation

### File Organization
- One class per file
- Proper file naming (class-{name}.php)
- Organized directory structure
- Separation of concerns

### Hooks and Filters
- Prefixed with `social_feed_plugin_`
- Documented in code
- Follow WordPress naming conventions

### Database
- Uses WordPress Options API
- No custom tables
- Proper data sanitization
- Automatic cleanup on uninstall

## Localization

### Text Domain
- Text domain: `social-feed-plugin`
- Domain path: `/languages`

### Translation Functions
- `__()` for simple strings
- `_e()` for echoed strings
- `esc_html__()` for escaped translations
- `esc_html_e()` for escaped echoed translations

### POT File
- Located in `/languages/social-feed-plugin.pot`
- Contains all translatable strings
- Ready for translation services

## Performance Optimization

### Caching Strategy
- Reduces API calls by 99%
- Improves page load time
- Configurable cache duration

### Asset Loading
- CSS/JS only loaded when needed
- Minification ready
- Version numbers for cache busting

### Database Queries
- Minimal database usage
- Uses WordPress transients where appropriate
- No complex queries

### Image Optimization
- Lazy loading for images
- Proper image sizing attributes
- Video loading on demand

## Extensibility

### Hooks Available
- `social_feed_plugin_clear_cache` - Cron hook for cache clearing
- Standard WordPress hooks for filters and actions

### Override System
- Templates can be overridden in theme
- CSS easily customizable
- Shortcode attributes for customization

### Filter Points
The plugin is designed to be extended with custom filters in future versions:
- Feed data normalization
- Display templates
- Cache duration
- API endpoints

## Testing Recommendations

### Manual Testing
1. **Installation Test**
   - Fresh install
   - Activation
   - Deactivation
   - Uninstall

2. **Configuration Test**
   - Add valid credentials
   - Add invalid credentials
   - Save settings
   - Clear cache

3. **Display Test**
   - Add shortcode to post
   - Test with different attributes
   - Test on different themes
   - Mobile responsiveness

4. **Error Handling Test**
   - Invalid API keys
   - Network failures
   - Rate limiting
   - Empty responses

### Automated Testing
Future versions could include:
- PHPUnit tests for API classes
- Integration tests for WordPress functions
- JavaScript tests for frontend
- End-to-end tests with Playwright

## Browser Compatibility

### Supported Browsers
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

### Progressive Enhancement
- Core functionality works without JavaScript
- CSS Grid with fallbacks
- Semantic HTML structure

## Deployment Checklist

- [ ] All PHP files pass syntax check
- [ ] No debug statements in production code
- [ ] API version constants configured
- [ ] Default settings appropriate
- [ ] Documentation up to date
- [ ] Security review completed
- [ ] Performance tested
- [ ] Browser testing completed
- [ ] Mobile testing completed
- [ ] Plugin headers correct
- [ ] Version numbers updated
- [ ] README.md complete
- [ ] USAGE.md complete

## Future Enhancements

### Potential Features
1. **Additional Platforms**
   - YouTube
   - TikTok
   - Pinterest
   - Reddit

2. **Advanced Features**
   - Post filtering by hashtags
   - Custom post types
   - Widget support
   - Gutenberg block
   - Elementor widget

3. **Analytics**
   - Post engagement tracking
   - Click tracking
   - Popular posts

4. **Moderation**
   - Hide specific posts
   - Manual post selection
   - Content filtering

5. **Performance**
   - CDN integration
   - Image optimization
   - Lazy loading improvements

## Known Limitations

1. **Token Management**
   - Tokens must be manually refreshed
   - No automatic token renewal

2. **API Rate Limits**
   - Subject to platform rate limits
   - Free tier limitations apply

3. **Single Account**
   - One account per platform
   - No multi-account support

4. **Real-time Updates**
   - Cache delay means not real-time
   - Manual cache clear required for immediate updates

## Support and Maintenance

### Version Strategy
- Semantic versioning (MAJOR.MINOR.PATCH)
- Backward compatibility maintained
- Deprecated features marked clearly

### Update Process
1. Test on staging environment
2. Update version numbers
3. Update changelog
4. Tag release in git
5. Deploy to production

### Issue Tracking
- GitHub Issues for bug reports
- Feature requests via GitHub
- Security issues via private channel

## Credits and Attribution

### Third-party Resources
- Social media icons: SVG from respective platforms
- Design inspiration: WordPress admin interface
- Code structure: WordPress Plugin Boilerplate

### License
GPL-2.0+ - Compatible with WordPress

---

**Last Updated:** December 30, 2024
**Plugin Version:** 1.0.0
**WordPress Version:** 5.0+
**PHP Version:** 7.2+
