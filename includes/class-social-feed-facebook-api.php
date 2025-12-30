<?php
/**
 * Facebook API integration
 *
 * @package    Social_Feed_Plugin
 * @subpackage Social_Feed_Plugin/includes
 */

class Social_Feed_Facebook_API extends Social_Feed_API_Base {

	/**
	 * Initialize the class.
	 */
	public function __construct() {
		$this->platform = 'facebook';
		parent::__construct();
	}

	/**
	 * Get Facebook API credentials.
	 *
	 * @return array
	 */
	protected function get_credentials() {
		$options = get_option( 'social_feed_plugin_options' );
		
		$credentials = array();
		if ( ! empty( $options['facebook_app_id'] ) && ! empty( $options['facebook_app_secret'] ) ) {
			$credentials['app_id'] = sanitize_text_field( $options['facebook_app_id'] );
			$credentials['app_secret'] = sanitize_text_field( $options['facebook_app_secret'] );
			$credentials['access_token'] = ! empty( $options['facebook_access_token'] ) 
				? sanitize_text_field( $options['facebook_access_token'] ) 
				: '';
		}
		
		return $credentials;
	}

	/**
	 * Fetch Facebook feed.
	 *
	 * @return array|WP_Error
	 */
	public function fetch_feed() {
		if ( ! $this->is_configured() ) {
			return new WP_Error( 'not_configured', __( 'Facebook API is not configured', 'social-feed-plugin' ) );
		}

		// Check cache first
		$cached_data = $this->cache->get( $this->get_cache_key() );
		if ( $cached_data !== false ) {
			return $cached_data;
		}

		$options = get_option( 'social_feed_plugin_options' );
		$page_id = ! empty( $options['facebook_page_id'] ) ? sanitize_text_field( $options['facebook_page_id'] ) : '';
		
		if ( empty( $page_id ) ) {
			return new WP_Error( 'missing_page_id', __( 'Facebook Page ID is required', 'social-feed-plugin' ) );
		}

		$access_token = $this->credentials['access_token'];
		if ( empty( $access_token ) ) {
			$access_token = $this->credentials['app_id'] . '|' . $this->credentials['app_secret'];
		}

		$url = add_query_arg(
			array(
				'fields' => 'id,message,created_time,full_picture,type,permalink_url',
				'access_token' => $access_token,
				'limit' => 25,
			),
			'https://graph.facebook.com/v18.0/' . $page_id . '/posts'
		);

		$data = $this->make_request( $url );

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$normalized_data = $this->normalize_data( $data );
		$this->cache->set( $this->get_cache_key(), $normalized_data );

		return $normalized_data;
	}

	/**
	 * Normalize Facebook data.
	 *
	 * @param array $raw_data Raw Facebook API data.
	 * @return array Normalized data.
	 */
	protected function normalize_data( $raw_data ) {
		$posts = array();

		if ( ! isset( $raw_data['data'] ) || ! is_array( $raw_data['data'] ) ) {
			return $posts;
		}

		foreach ( $raw_data['data'] as $item ) {
			$posts[] = array(
				'id' => isset( $item['id'] ) ? sanitize_text_field( $item['id'] ) : '',
				'platform' => 'facebook',
				'text' => isset( $item['message'] ) ? sanitize_textarea_field( $item['message'] ) : '',
				'created_time' => isset( $item['created_time'] ) ? strtotime( $item['created_time'] ) : time(),
				'image' => isset( $item['full_picture'] ) ? esc_url_raw( $item['full_picture'] ) : '',
				'video' => '',
				'link' => isset( $item['permalink_url'] ) ? esc_url_raw( $item['permalink_url'] ) : '',
				'type' => isset( $item['type'] ) ? sanitize_text_field( $item['type'] ) : 'status',
			);
		}

		return $posts;
	}
}
