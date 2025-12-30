<?php
/**
 * Fired during plugin deactivation
 *
 * @package    Social_Feed_Plugin
 * @subpackage Social_Feed_Plugin/includes
 */

class Social_Feed_Deactivator {

	/**
	 * Plugin deactivation.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// Clear scheduled events if any
		wp_clear_scheduled_hook( 'social_feed_plugin_clear_cache' );
	}
}
