<?php
/**
 * Fired during plugin activation
 *
 * @package    Social_Feed_Plugin
 * @subpackage Social_Feed_Plugin/includes
 */

class Social_Feed_Activator {

	/**
	 * Plugin activation.
	 *
	 * Creates necessary database tables and default options.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Set default options
		$default_options = array(
			'cache_duration' => 3600, // 1 hour
			'posts_per_page' => 10,
			'enable_facebook' => false,
			'enable_instagram' => false,
			'enable_twitter' => false,
			'enable_linkedin' => false,
		);
		
		add_option( 'social_feed_plugin_options', $default_options );
		
		// Create cache directory if it doesn't exist
		$upload_dir = wp_upload_dir();
		$cache_dir = $upload_dir['basedir'] . '/social-feed-cache';
		if ( ! file_exists( $cache_dir ) ) {
			wp_mkdir_p( $cache_dir );
			// Add .htaccess for security
			file_put_contents( $cache_dir . '/.htaccess', 'deny from all' );
		}
	}
}
