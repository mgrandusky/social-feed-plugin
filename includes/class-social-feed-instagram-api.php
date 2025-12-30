<?php
/**
 * Instagram API integration
 *
 * @package    Social_Feed_Plugin
 * @subpackage Social_Feed_Plugin/includes
 */

class Social_Feed_Instagram_API extends Social_Feed_API_Base {

	/**
	 * Initialize the class.
	 */
	public function __construct() {
		$this->platform = 'instagram';
		parent::__construct();
	}

	/**
	 * Get Instagram API credentials.
	 *
	 * @return array
	 */
	protected function get_credentials() {
		$options = get_option( 'social_feed_plugin_options' );
		
		$credentials = array();
		if ( ! empty( $options['instagram_access_token'] ) ) {
			$credentials['access_token'] = sanitize_text_field( $options['instagram_access_token'] );
		}
		
		return $credentials;
	}

	/**
	 * Fetch Instagram feed.
	 *
	 * @return array|WP_Error
	 */
	public function fetch_feed() {
		if ( ! $this->is_configured() ) {
			return new WP_Error( 'not_configured', __( 'Instagram API is not configured', 'social-feed-plugin' ) );
		}

		// Check cache first
		$cached_data = $this->cache->get( $this->get_cache_key() );
		if ( $cached_data !== false ) {
			return $cached_data;
		}

		$url = add_query_arg(
			array(
				'fields' => 'id,caption,media_type,media_url,permalink,thumbnail_url,timestamp',
				'access_token' => $this->credentials['access_token'],
				'limit' => 25,
			),
			'https://graph.instagram.com/me/media'
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
	 * Normalize Instagram data.
	 *
	 * @param array $raw_data Raw Instagram API data.
	 * @return array Normalized data.
	 */
	protected function normalize_data( $raw_data ) {
		$posts = array();

		if ( ! isset( $raw_data['data'] ) || ! is_array( $raw_data['data'] ) ) {
			return $posts;
		}

		foreach ( $raw_data['data'] as $item ) {
			$media_url = '';
			$video_url = '';
			
			if ( isset( $item['media_type'] ) && $item['media_type'] === 'VIDEO' ) {
				$video_url = isset( $item['media_url'] ) ? esc_url_raw( $item['media_url'] ) : '';
				$media_url = isset( $item['thumbnail_url'] ) ? esc_url_raw( $item['thumbnail_url'] ) : '';
			} else {
				$media_url = isset( $item['media_url'] ) ? esc_url_raw( $item['media_url'] ) : '';
			}

			$posts[] = array(
				'id' => isset( $item['id'] ) ? sanitize_text_field( $item['id'] ) : '',
				'platform' => 'instagram',
				'text' => isset( $item['caption'] ) ? sanitize_textarea_field( $item['caption'] ) : '',
				'created_time' => isset( $item['timestamp'] ) ? strtotime( $item['timestamp'] ) : time(),
				'image' => $media_url,
				'video' => $video_url,
				'link' => isset( $item['permalink'] ) ? esc_url_raw( $item['permalink'] ) : '',
				'type' => isset( $item['media_type'] ) ? strtolower( sanitize_text_field( $item['media_type'] ) ) : 'image',
			);
		}

		return $posts;
	}
}
