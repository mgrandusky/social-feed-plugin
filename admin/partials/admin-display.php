<?php
/**
 * Admin settings page template
 *
 * @package    Social_Feed_Plugin
 * @subpackage Social_Feed_Plugin/admin/partials
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	
	<div class="notice notice-info">
		<p>
			<strong><?php esc_html_e( 'Usage:', 'social-feed-plugin' ); ?></strong>
			<?php esc_html_e( 'Add the shortcode', 'social-feed-plugin' ); ?>
			<code>[social_feeds]</code>
			<?php esc_html_e( 'to any post or page to display your social media feeds.', 'social-feed-plugin' ); ?>
		</p>
	</div>

	<form method="post" action="options.php">
		<?php
		settings_fields( 'social_feed_plugin_options' );
		do_settings_sections( 'social-feed-plugin' );
		submit_button();
		?>
	</form>

	<hr>

	<h2><?php esc_html_e( 'Cache Management', 'social-feed-plugin' ); ?></h2>
	<p><?php esc_html_e( 'Clear the cache to force refresh of all social media feeds.', 'social-feed-plugin' ); ?></p>
	<form method="post" action="">
		<?php wp_nonce_field( 'social_feed_clear_cache' ); ?>
		<input type="submit" name="clear_cache" class="button button-secondary" value="<?php esc_attr_e( 'Clear Cache', 'social-feed-plugin' ); ?>">
	</form>

	<hr>

	<h2><?php esc_html_e( 'API Setup Instructions', 'social-feed-plugin' ); ?></h2>
	
	<h3><?php esc_html_e( 'Facebook', 'social-feed-plugin' ); ?></h3>
	<ol>
		<li><?php esc_html_e( 'Go to Facebook Developers (developers.facebook.com)', 'social-feed-plugin' ); ?></li>
		<li><?php esc_html_e( 'Create a new app and get your App ID and App Secret', 'social-feed-plugin' ); ?></li>
		<li><?php esc_html_e( 'Add the Facebook Page ID you want to display', 'social-feed-plugin' ); ?></li>
		<li><?php esc_html_e( 'Optionally generate a Page Access Token for better functionality', 'social-feed-plugin' ); ?></li>
	</ol>

	<h3><?php esc_html_e( 'Instagram', 'social-feed-plugin' ); ?></h3>
	<ol>
		<li><?php esc_html_e( 'Use Instagram Basic Display API or Instagram Graph API', 'social-feed-plugin' ); ?></li>
		<li><?php esc_html_e( 'Create an app at developers.facebook.com', 'social-feed-plugin' ); ?></li>
		<li><?php esc_html_e( 'Generate a long-lived access token', 'social-feed-plugin' ); ?></li>
		<li><?php esc_html_e( 'Enter the access token in the field above', 'social-feed-plugin' ); ?></li>
	</ol>

	<h3><?php esc_html_e( 'Twitter', 'social-feed-plugin' ); ?></h3>
	<ol>
		<li><?php esc_html_e( 'Go to Twitter Developer Portal (developer.twitter.com)', 'social-feed-plugin' ); ?></li>
		<li><?php esc_html_e( 'Create a new project and app', 'social-feed-plugin' ); ?></li>
		<li><?php esc_html_e( 'Generate a Bearer Token with read permissions', 'social-feed-plugin' ); ?></li>
		<li><?php esc_html_e( 'Enter your Twitter username (without @)', 'social-feed-plugin' ); ?></li>
		<li><strong><?php esc_html_e( 'Important:', 'social-feed-plugin' ); ?></strong> <?php esc_html_e( 'Make sure your app has access to Twitter API v2 and the bearer token has not expired', 'social-feed-plugin' ); ?></li>
	</ol>
	<p><em><?php esc_html_e( 'If you receive a 401 error, verify that:', 'social-feed-plugin' ); ?></em></p>
	<ul>
		<li><?php esc_html_e( 'Your bearer token is correct and has not expired', 'social-feed-plugin' ); ?></li>
		<li><?php esc_html_e( 'Your Twitter app has the necessary permissions (tweet.read, users.read)', 'social-feed-plugin' ); ?></li>
		<li><?php esc_html_e( 'Your app has been approved for Essential, Elevated, or higher access level', 'social-feed-plugin' ); ?></li>
	</ul>

	<h3><?php esc_html_e( 'LinkedIn', 'social-feed-plugin' ); ?></h3>
	<ol>
		<li><?php esc_html_e( 'Go to LinkedIn Developer Platform (developer.linkedin.com)', 'social-feed-plugin' ); ?></li>
		<li><?php esc_html_e( 'Create a new app', 'social-feed-plugin' ); ?></li>
		<li><?php esc_html_e( 'Request access to the required API products', 'social-feed-plugin' ); ?></li>
		<li><?php esc_html_e( 'Generate an access token with proper scopes (r_liteprofile, r_emailaddress, w_member_social)', 'social-feed-plugin' ); ?></li>
	</ol>

	<h3><?php esc_html_e( 'YouTube', 'social-feed-plugin' ); ?></h3>
	<ol>
		<li><?php esc_html_e( 'Go to Google Cloud Console (console.cloud.google.com)', 'social-feed-plugin' ); ?></li>
		<li><?php esc_html_e( 'Create a new project or select an existing one', 'social-feed-plugin' ); ?></li>
		<li><?php esc_html_e( 'Enable the YouTube Data API v3', 'social-feed-plugin' ); ?></li>
		<li><?php esc_html_e( 'Create credentials (API Key) and restrict it to YouTube Data API v3', 'social-feed-plugin' ); ?></li>
		<li><?php esc_html_e( 'Find your Channel ID by going to youtube.com/account_advanced', 'social-feed-plugin' ); ?></li>
	</ol>
</div>
