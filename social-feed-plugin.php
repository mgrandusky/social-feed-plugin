<?php
/**
 * Plugin Name: Social Feed Plugin
 * Plugin URI: https://github.com/mgrandusky/social-feed-plugin
 * Description: Aggregates social media feeds from Facebook, Instagram, Twitter, and LinkedIn into a single display block for WordPress sites.
 * Version: 1.0.0
 * Author: Social Feed Plugin Team
 * Author URI: https://github.com/mgrandusky/social-feed-plugin
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: social-feed-plugin
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'SOCIAL_FEED_PLUGIN_VERSION', '1.0.0' );
define( 'SOCIAL_FEED_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'SOCIAL_FEED_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_social_feed_plugin() {
	require_once SOCIAL_FEED_PLUGIN_PATH . 'includes/class-social-feed-activator.php';
	Social_Feed_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_social_feed_plugin() {
	require_once SOCIAL_FEED_PLUGIN_PATH . 'includes/class-social-feed-deactivator.php';
	Social_Feed_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_social_feed_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_social_feed_plugin' );

/**
 * The core plugin class.
 */
require SOCIAL_FEED_PLUGIN_PATH . 'includes/class-social-feed-plugin.php';

/**
 * Begins execution of the plugin.
 */
function run_social_feed_plugin() {
	$plugin = new Social_Feed_Plugin();
	$plugin->run();
}
run_social_feed_plugin();
