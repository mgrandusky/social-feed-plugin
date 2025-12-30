<?php
/**
 * The public-facing functionality of the plugin
 *
 * @package    Social_Feed_Plugin
 * @subpackage Social_Feed_Plugin/public
 */

class Social_Feed_Public {

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
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name,
			SOCIAL_FEED_PLUGIN_URL . 'assets/css/public.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			$this->plugin_name,
			SOCIAL_FEED_PLUGIN_URL . 'assets/js/public.js',
			array( 'jquery' ),
			$this->version,
			false
		);
	}

	/**
	 * Shortcode callback to display social feeds.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function social_feeds_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'limit' => 0, // 0 means use default from settings
				'platforms' => '', // Comma-separated list, empty means all enabled
			),
			$atts,
			'social_feeds'
		);

		$options = get_option( 'social_feed_plugin_options' );
		$limit = intval( $atts['limit'] ) > 0 ? intval( $atts['limit'] ) : ( isset( $options['posts_per_page'] ) ? intval( $options['posts_per_page'] ) : 10 );

		// Determine which platforms to fetch
		$platforms_to_fetch = array();
		if ( ! empty( $atts['platforms'] ) ) {
			$platforms_to_fetch = array_map( 'trim', explode( ',', $atts['platforms'] ) );
		} else {
			// Use all enabled platforms
			if ( ! empty( $options['enable_facebook'] ) ) {
				$platforms_to_fetch[] = 'facebook';
			}
			if ( ! empty( $options['enable_instagram'] ) ) {
				$platforms_to_fetch[] = 'instagram';
			}
			if ( ! empty( $options['enable_twitter'] ) ) {
				$platforms_to_fetch[] = 'twitter';
			}
			if ( ! empty( $options['enable_linkedin'] ) ) {
				$platforms_to_fetch[] = 'linkedin';
			}
		}

		if ( empty( $platforms_to_fetch ) ) {
			return '<div class="social-feed-error">' . esc_html__( 'No social media platforms are enabled. Please configure the plugin settings.', 'social-feed-plugin' ) . '</div>';
		}

		// Fetch feeds from all platforms
		$all_posts = array();
		$errors = array();

		foreach ( $platforms_to_fetch as $platform ) {
			$posts = $this->fetch_platform_feed( $platform );
			if ( is_wp_error( $posts ) ) {
				$errors[] = sprintf(
					/* translators: %1$s: platform name, %2$s: error message */
					__( '%1$s: %2$s', 'social-feed-plugin' ),
					ucfirst( $platform ),
					$posts->get_error_message()
				);
			} elseif ( is_array( $posts ) ) {
				$all_posts = array_merge( $all_posts, $posts );
			}
		}

		// Sort by created_time descending
		usort( $all_posts, function( $a, $b ) {
			return $b['created_time'] - $a['created_time'];
		});

		// Limit the number of posts
		$all_posts = array_slice( $all_posts, 0, $limit );

		// Generate HTML output
		ob_start();
		include SOCIAL_FEED_PLUGIN_PATH . 'public/partials/feed-display.php';
		return ob_get_clean();
	}

	/**
	 * Fetch feed for a specific platform.
	 *
	 * @param string $platform Platform name.
	 * @return array|WP_Error
	 */
	private function fetch_platform_feed( $platform ) {
		switch ( $platform ) {
			case 'facebook':
				$api = new Social_Feed_Facebook_API();
				break;
			case 'instagram':
				$api = new Social_Feed_Instagram_API();
				break;
			case 'twitter':
				$api = new Social_Feed_Twitter_API();
				break;
			case 'linkedin':
				$api = new Social_Feed_LinkedIn_API();
				break;
			default:
				return new WP_Error( 'invalid_platform', __( 'Invalid platform specified', 'social-feed-plugin' ) );
		}

		return $api->fetch_feed();
	}
}
