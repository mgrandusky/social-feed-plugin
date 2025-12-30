<?php
/**
 * The admin-specific functionality of the plugin
 *
 * @package    Social_Feed_Plugin
 * @subpackage Social_Feed_Plugin/admin
 */

class Social_Feed_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @var      string
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @var      string
	 */
	private $version;

	/**
	 * Initialize the class.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name,
			SOCIAL_FEED_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			$this->plugin_name,
			SOCIAL_FEED_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			$this->version,
			false
		);
	}

	/**
	 * Add options page to admin menu.
	 */
	public function add_plugin_admin_menu() {
		add_options_page(
			__( 'Social Feed Plugin Settings', 'social-feed-plugin' ),
			__( 'Social Feeds', 'social-feed-plugin' ),
			'manage_options',
			'social-feed-plugin',
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Render the settings page.
	 */
	public function display_plugin_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'social-feed-plugin' ) );
		}

		// Handle cache clearing
		if ( isset( $_POST['clear_cache'] ) && check_admin_referer( 'social_feed_clear_cache' ) ) {
			$cache = new Social_Feed_Cache();
			$cache->clear_all();
			echo '<div class="notice notice-success"><p>' . esc_html__( 'Cache cleared successfully!', 'social-feed-plugin' ) . '</p></div>';
		}

		include_once SOCIAL_FEED_PLUGIN_PATH . 'admin/partials/admin-display.php';
	}

	/**
	 * Register plugin settings.
	 */
	public function register_settings() {
		register_setting(
			'social_feed_plugin_options',
			'social_feed_plugin_options',
			array( $this, 'validate_options' )
		);

		// General Settings
		add_settings_section(
			'social_feed_general_section',
			__( 'General Settings', 'social-feed-plugin' ),
			array( $this, 'general_section_callback' ),
			'social-feed-plugin'
		);

		add_settings_field(
			'cache_duration',
			__( 'Cache Duration (seconds)', 'social-feed-plugin' ),
			array( $this, 'cache_duration_callback' ),
			'social-feed-plugin',
			'social_feed_general_section'
		);

		add_settings_field(
			'posts_per_page',
			__( 'Posts Per Page', 'social-feed-plugin' ),
			array( $this, 'posts_per_page_callback' ),
			'social-feed-plugin',
			'social_feed_general_section'
		);

		// Facebook Settings
		add_settings_section(
			'social_feed_facebook_section',
			__( 'Facebook Settings', 'social-feed-plugin' ),
			array( $this, 'facebook_section_callback' ),
			'social-feed-plugin'
		);

		add_settings_field(
			'enable_facebook',
			__( 'Enable Facebook', 'social-feed-plugin' ),
			array( $this, 'enable_facebook_callback' ),
			'social-feed-plugin',
			'social_feed_facebook_section'
		);

		add_settings_field(
			'facebook_app_id',
			__( 'Facebook App ID', 'social-feed-plugin' ),
			array( $this, 'facebook_app_id_callback' ),
			'social-feed-plugin',
			'social_feed_facebook_section'
		);

		add_settings_field(
			'facebook_app_secret',
			__( 'Facebook App Secret', 'social-feed-plugin' ),
			array( $this, 'facebook_app_secret_callback' ),
			'social-feed-plugin',
			'social_feed_facebook_section'
		);

		add_settings_field(
			'facebook_access_token',
			__( 'Facebook Access Token (Optional)', 'social-feed-plugin' ),
			array( $this, 'facebook_access_token_callback' ),
			'social-feed-plugin',
			'social_feed_facebook_section'
		);

		add_settings_field(
			'facebook_page_id',
			__( 'Facebook Page ID', 'social-feed-plugin' ),
			array( $this, 'facebook_page_id_callback' ),
			'social-feed-plugin',
			'social_feed_facebook_section'
		);

		// Instagram Settings
		add_settings_section(
			'social_feed_instagram_section',
			__( 'Instagram Settings', 'social-feed-plugin' ),
			array( $this, 'instagram_section_callback' ),
			'social-feed-plugin'
		);

		add_settings_field(
			'enable_instagram',
			__( 'Enable Instagram', 'social-feed-plugin' ),
			array( $this, 'enable_instagram_callback' ),
			'social-feed-plugin',
			'social_feed_instagram_section'
		);

		add_settings_field(
			'instagram_access_token',
			__( 'Instagram Access Token', 'social-feed-plugin' ),
			array( $this, 'instagram_access_token_callback' ),
			'social-feed-plugin',
			'social_feed_instagram_section'
		);

		// Twitter Settings
		add_settings_section(
			'social_feed_twitter_section',
			__( 'Twitter Settings', 'social-feed-plugin' ),
			array( $this, 'twitter_section_callback' ),
			'social-feed-plugin'
		);

		add_settings_field(
			'enable_twitter',
			__( 'Enable Twitter', 'social-feed-plugin' ),
			array( $this, 'enable_twitter_callback' ),
			'social-feed-plugin',
			'social_feed_twitter_section'
		);

		add_settings_field(
			'twitter_bearer_token',
			__( 'Twitter Bearer Token', 'social-feed-plugin' ),
			array( $this, 'twitter_bearer_token_callback' ),
			'social-feed-plugin',
			'social_feed_twitter_section'
		);

		add_settings_field(
			'twitter_username',
			__( 'Twitter Username', 'social-feed-plugin' ),
			array( $this, 'twitter_username_callback' ),
			'social-feed-plugin',
			'social_feed_twitter_section'
		);

		// LinkedIn Settings
		add_settings_section(
			'social_feed_linkedin_section',
			__( 'LinkedIn Settings', 'social-feed-plugin' ),
			array( $this, 'linkedin_section_callback' ),
			'social-feed-plugin'
		);

		add_settings_field(
			'enable_linkedin',
			__( 'Enable LinkedIn', 'social-feed-plugin' ),
			array( $this, 'enable_linkedin_callback' ),
			'social-feed-plugin',
			'social_feed_linkedin_section'
		);

		add_settings_field(
			'linkedin_access_token',
			__( 'LinkedIn Access Token', 'social-feed-plugin' ),
			array( $this, 'linkedin_access_token_callback' ),
			'social-feed-plugin',
			'social_feed_linkedin_section'
		);

		// YouTube Settings
		add_settings_section(
			'social_feed_youtube_section',
			__( 'YouTube Settings', 'social-feed-plugin' ),
			array( $this, 'youtube_section_callback' ),
			'social-feed-plugin'
		);

		add_settings_field(
			'enable_youtube',
			__( 'Enable YouTube', 'social-feed-plugin' ),
			array( $this, 'enable_youtube_callback' ),
			'social-feed-plugin',
			'social_feed_youtube_section'
		);

		add_settings_field(
			'youtube_api_key',
			__( 'YouTube API Key', 'social-feed-plugin' ),
			array( $this, 'youtube_api_key_callback' ),
			'social-feed-plugin',
			'social_feed_youtube_section'
		);

		add_settings_field(
			'youtube_channel_id',
			__( 'YouTube Channel ID', 'social-feed-plugin' ),
			array( $this, 'youtube_channel_id_callback' ),
			'social-feed-plugin',
			'social_feed_youtube_section'
		);
	}

	/**
	 * Validate and sanitize options.
	 *
	 * @param array $input Input options.
	 * @return array Sanitized options.
	 */
	public function validate_options( $input ) {
		$output = array();

		// General settings
		$output['cache_duration'] = isset( $input['cache_duration'] ) ? absint( $input['cache_duration'] ) : 3600;
		$output['posts_per_page'] = isset( $input['posts_per_page'] ) ? absint( $input['posts_per_page'] ) : 10;

		// Facebook
		$output['enable_facebook'] = isset( $input['enable_facebook'] ) ? (bool) $input['enable_facebook'] : false;
		$output['facebook_app_id'] = isset( $input['facebook_app_id'] ) ? sanitize_text_field( $input['facebook_app_id'] ) : '';
		$output['facebook_app_secret'] = isset( $input['facebook_app_secret'] ) ? sanitize_text_field( $input['facebook_app_secret'] ) : '';
		$output['facebook_access_token'] = isset( $input['facebook_access_token'] ) ? sanitize_text_field( $input['facebook_access_token'] ) : '';
		$output['facebook_page_id'] = isset( $input['facebook_page_id'] ) ? sanitize_text_field( $input['facebook_page_id'] ) : '';

		// Instagram
		$output['enable_instagram'] = isset( $input['enable_instagram'] ) ? (bool) $input['enable_instagram'] : false;
		$output['instagram_access_token'] = isset( $input['instagram_access_token'] ) ? sanitize_text_field( $input['instagram_access_token'] ) : '';

		// Twitter
		$output['enable_twitter'] = isset( $input['enable_twitter'] ) ? (bool) $input['enable_twitter'] : false;
		$output['twitter_bearer_token'] = isset( $input['twitter_bearer_token'] ) ? sanitize_text_field( $input['twitter_bearer_token'] ) : '';
		$output['twitter_username'] = isset( $input['twitter_username'] ) ? sanitize_text_field( $input['twitter_username'] ) : '';

		// LinkedIn
		$output['enable_linkedin'] = isset( $input['enable_linkedin'] ) ? (bool) $input['enable_linkedin'] : false;
		$output['linkedin_access_token'] = isset( $input['linkedin_access_token'] ) ? sanitize_text_field( $input['linkedin_access_token'] ) : '';

		// YouTube
		$output['enable_youtube'] = isset( $input['enable_youtube'] ) ? (bool) $input['enable_youtube'] : false;
		$output['youtube_api_key'] = isset( $input['youtube_api_key'] ) ? sanitize_text_field( $input['youtube_api_key'] ) : '';
		$output['youtube_channel_id'] = isset( $input['youtube_channel_id'] ) ? sanitize_text_field( $input['youtube_channel_id'] ) : '';

		return $output;
	}

	// Section callbacks
	public function general_section_callback() {
		echo '<p>' . esc_html__( 'Configure general plugin settings.', 'social-feed-plugin' ) . '</p>';
	}

	public function facebook_section_callback() {
		echo '<p>' . esc_html__( 'Configure Facebook API settings. Get your credentials from the Facebook Developer Console.', 'social-feed-plugin' ) . '</p>';
	}

	public function instagram_section_callback() {
		echo '<p>' . esc_html__( 'Configure Instagram API settings. Use Instagram Basic Display API or Instagram Graph API.', 'social-feed-plugin' ) . '</p>';
	}

	public function twitter_section_callback() {
		echo '<p>' . esc_html__( 'Configure Twitter API v2 settings. Get your bearer token from the Twitter Developer Portal.', 'social-feed-plugin' ) . '</p>';
	}

	public function linkedin_section_callback() {
		echo '<p>' . esc_html__( 'Configure LinkedIn API settings. Get your access token from the LinkedIn Developer Platform.', 'social-feed-plugin' ) . '</p>';
	}

	// Field callbacks
	public function cache_duration_callback() {
		$options = get_option( 'social_feed_plugin_options' );
		$value = isset( $options['cache_duration'] ) ? $options['cache_duration'] : 3600;
		echo '<input type="number" name="social_feed_plugin_options[cache_duration]" value="' . esc_attr( $value ) . '" min="60" />';
		echo '<p class="description">' . esc_html__( 'How long to cache API responses (in seconds). Default: 3600 (1 hour)', 'social-feed-plugin' ) . '</p>';
	}

	public function posts_per_page_callback() {
		$options = get_option( 'social_feed_plugin_options' );
		$value = isset( $options['posts_per_page'] ) ? $options['posts_per_page'] : 10;
		echo '<input type="number" name="social_feed_plugin_options[posts_per_page]" value="' . esc_attr( $value ) . '" min="1" max="50" />';
		echo '<p class="description">' . esc_html__( 'Number of posts to display. Default: 10', 'social-feed-plugin' ) . '</p>';
	}

	public function enable_facebook_callback() {
		$options = get_option( 'social_feed_plugin_options' );
		$checked = isset( $options['enable_facebook'] ) && $options['enable_facebook'] ? 'checked' : '';
		echo '<input type="checkbox" name="social_feed_plugin_options[enable_facebook]" value="1" ' . esc_attr( $checked ) . ' />';
	}

	public function facebook_app_id_callback() {
		$options = get_option( 'social_feed_plugin_options' );
		$value = isset( $options['facebook_app_id'] ) ? $options['facebook_app_id'] : '';
		echo '<input type="text" name="social_feed_plugin_options[facebook_app_id]" value="' . esc_attr( $value ) . '" class="regular-text" />';
	}

	public function facebook_app_secret_callback() {
		$options = get_option( 'social_feed_plugin_options' );
		$value = isset( $options['facebook_app_secret'] ) ? $options['facebook_app_secret'] : '';
		echo '<input type="password" name="social_feed_plugin_options[facebook_app_secret]" value="' . esc_attr( $value ) . '" class="regular-text" />';
	}

	public function facebook_access_token_callback() {
		$options = get_option( 'social_feed_plugin_options' );
		$value = isset( $options['facebook_access_token'] ) ? $options['facebook_access_token'] : '';
		echo '<input type="text" name="social_feed_plugin_options[facebook_access_token]" value="' . esc_attr( $value ) . '" class="regular-text" />';
		echo '<p class="description">' . esc_html__( 'Optional. If not provided, App ID and Secret will be used.', 'social-feed-plugin' ) . '</p>';
	}

	public function facebook_page_id_callback() {
		$options = get_option( 'social_feed_plugin_options' );
		$value = isset( $options['facebook_page_id'] ) ? $options['facebook_page_id'] : '';
		echo '<input type="text" name="social_feed_plugin_options[facebook_page_id]" value="' . esc_attr( $value ) . '" class="regular-text" />';
	}

	public function enable_instagram_callback() {
		$options = get_option( 'social_feed_plugin_options' );
		$checked = isset( $options['enable_instagram'] ) && $options['enable_instagram'] ? 'checked' : '';
		echo '<input type="checkbox" name="social_feed_plugin_options[enable_instagram]" value="1" ' . esc_attr( $checked ) . ' />';
	}

	public function instagram_access_token_callback() {
		$options = get_option( 'social_feed_plugin_options' );
		$value = isset( $options['instagram_access_token'] ) ? $options['instagram_access_token'] : '';
		echo '<input type="text" name="social_feed_plugin_options[instagram_access_token]" value="' . esc_attr( $value ) . '" class="regular-text" />';
	}

	public function enable_twitter_callback() {
		$options = get_option( 'social_feed_plugin_options' );
		$checked = isset( $options['enable_twitter'] ) && $options['enable_twitter'] ? 'checked' : '';
		echo '<input type="checkbox" name="social_feed_plugin_options[enable_twitter]" value="1" ' . esc_attr( $checked ) . ' />';
	}

	public function twitter_bearer_token_callback() {
		$options = get_option( 'social_feed_plugin_options' );
		$value = isset( $options['twitter_bearer_token'] ) ? $options['twitter_bearer_token'] : '';
		echo '<input type="text" name="social_feed_plugin_options[twitter_bearer_token]" value="' . esc_attr( $value ) . '" class="regular-text" />';
	}

	public function twitter_username_callback() {
		$options = get_option( 'social_feed_plugin_options' );
		$value = isset( $options['twitter_username'] ) ? $options['twitter_username'] : '';
		echo '<input type="text" name="social_feed_plugin_options[twitter_username]" value="' . esc_attr( $value ) . '" class="regular-text" />';
		echo '<p class="description">' . esc_html__( 'Twitter username without @', 'social-feed-plugin' ) . '</p>';
	}

	public function enable_linkedin_callback() {
		$options = get_option( 'social_feed_plugin_options' );
		$checked = isset( $options['enable_linkedin'] ) && $options['enable_linkedin'] ? 'checked' : '';
		echo '<input type="checkbox" name="social_feed_plugin_options[enable_linkedin]" value="1" ' . esc_attr( $checked ) . ' />';
	}

	public function linkedin_access_token_callback() {
		$options = get_option( 'social_feed_plugin_options' );
		$value = isset( $options['linkedin_access_token'] ) ? $options['linkedin_access_token'] : '';
		echo '<input type="text" name="social_feed_plugin_options[linkedin_access_token]" value="' . esc_attr( $value ) . '" class="regular-text" />';
	}

	public function youtube_section_callback() {
		echo '<p>' . esc_html__( 'Configure YouTube Data API v3 settings. Get your API key from the Google Cloud Console.', 'social-feed-plugin' ) . '</p>';
	}

	public function enable_youtube_callback() {
		$options = get_option( 'social_feed_plugin_options' );
		$checked = isset( $options['enable_youtube'] ) && $options['enable_youtube'] ? 'checked' : '';
		echo '<input type="checkbox" name="social_feed_plugin_options[enable_youtube]" value="1" ' . esc_attr( $checked ) . ' />';
	}

	public function youtube_api_key_callback() {
		$options = get_option( 'social_feed_plugin_options' );
		$value = isset( $options['youtube_api_key'] ) ? $options['youtube_api_key'] : '';
		echo '<input type="text" name="social_feed_plugin_options[youtube_api_key]" value="' . esc_attr( $value ) . '" class="regular-text" />';
	}

	public function youtube_channel_id_callback() {
		$options = get_option( 'social_feed_plugin_options' );
		$value = isset( $options['youtube_channel_id'] ) ? $options['youtube_channel_id'] : '';
		echo '<input type="text" name="social_feed_plugin_options[youtube_channel_id]" value="' . esc_attr( $value ) . '" class="regular-text" />';
		echo '<p class="description">' . esc_html__( 'Your YouTube Channel ID (e.g., UCxxxxxxxxxxxxxx)', 'social-feed-plugin' ) . '</p>';
	}
}
