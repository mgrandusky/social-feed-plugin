<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package    Social_Feed_Plugin
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete plugin options
delete_option( 'social_feed_plugin_options' );

// Delete cache directory
$upload_dir = wp_upload_dir();
$cache_dir = $upload_dir['basedir'] . '/social-feed-cache';

if ( file_exists( $cache_dir ) ) {
	// Delete all cache files
	$files = glob( $cache_dir . '/*.cache' );
	if ( is_array( $files ) ) {
		foreach ( $files as $file ) {
			if ( is_file( $file ) ) {
				unlink( $file );
			}
		}
	}
	// Remove .htaccess if exists
	$htaccess = $cache_dir . '/.htaccess';
	if ( file_exists( $htaccess ) ) {
		unlink( $htaccess );
	}
	// Remove the directory
	rmdir( $cache_dir );
}

// Clear scheduled events
wp_clear_scheduled_hook( 'social_feed_plugin_clear_cache' );
