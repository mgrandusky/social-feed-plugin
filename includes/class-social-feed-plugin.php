<?php
/**
 * The core plugin class
 *
 * @package    Social_Feed_Plugin
 * @subpackage Social_Feed_Plugin/includes
 */

class Social_Feed_Plugin {

	/**
	 * The loader that's responsible for maintaining and registering all hooks.
	 *
	 * @var      Social_Feed_Loader
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @var      string
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @var      string
	 */
	protected $version;

	/**
	 * Initialize the core plugin.
	 */
	public function __construct() {
		$this->version = SOCIAL_FEED_PLUGIN_VERSION;
		$this->plugin_name = 'social-feed-plugin';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 */
	private function load_dependencies() {
		require_once SOCIAL_FEED_PLUGIN_PATH . 'includes/class-social-feed-loader.php';
		require_once SOCIAL_FEED_PLUGIN_PATH . 'includes/class-social-feed-i18n.php';
		require_once SOCIAL_FEED_PLUGIN_PATH . 'includes/class-social-feed-cache.php';
		require_once SOCIAL_FEED_PLUGIN_PATH . 'includes/class-social-feed-api-base.php';
		require_once SOCIAL_FEED_PLUGIN_PATH . 'includes/class-social-feed-facebook-api.php';
		require_once SOCIAL_FEED_PLUGIN_PATH . 'includes/class-social-feed-instagram-api.php';
		require_once SOCIAL_FEED_PLUGIN_PATH . 'includes/class-social-feed-twitter-api.php';
		require_once SOCIAL_FEED_PLUGIN_PATH . 'includes/class-social-feed-linkedin-api.php';
		require_once SOCIAL_FEED_PLUGIN_PATH . 'admin/class-social-feed-admin.php';
		require_once SOCIAL_FEED_PLUGIN_PATH . 'public/class-social-feed-public.php';

		$this->loader = new Social_Feed_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 */
	private function set_locale() {
		$plugin_i18n = new Social_Feed_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality.
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Social_Feed_Admin( $this->plugin_name, $this->version );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality.
	 */
	private function define_public_hooks() {
		$plugin_public = new Social_Feed_Public( $this->plugin_name, $this->version );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_shortcode( 'social_feeds', $plugin_public, 'social_feeds_shortcode' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it.
	 *
	 * @return    string
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string
	 */
	public function get_version() {
		return $this->version;
	}
}
