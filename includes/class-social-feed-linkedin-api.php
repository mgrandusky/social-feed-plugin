<?php
/**
 * LinkedIn API integration
 *
 * @package    Social_Feed_Plugin
 * @subpackage Social_Feed_Plugin/includes
 */

class Social_Feed_LinkedIn_API extends Social_Feed_API_Base {

	/**
	 * Initialize the class.
	 */
	public function __construct() {
		$this->platform = 'linkedin';
		parent::__construct();
	}

	/**
	 * Get LinkedIn API credentials.
	 *
	 * @return array
	 */
	protected function get_credentials() {
		$options = get_option( 'social_feed_plugin_options' );
		
		$credentials = array();
		if ( ! empty( $options['linkedin_access_token'] ) ) {
			$credentials['access_token'] = sanitize_text_field( $options['linkedin_access_token'] );
		}
		
		return $credentials;
	}

	/**
	 * Fetch LinkedIn feed.
	 *
	 * @return array|WP_Error
	 */
	public function fetch_feed() {
		if ( ! $this->is_configured() ) {
			return new WP_Error( 'not_configured', __( 'LinkedIn API is not configured', 'social-feed-plugin' ) );
		}

		// Check cache first
		$cached_data = $this->cache->get( $this->get_cache_key() );
		if ( $cached_data !== false ) {
			return $cached_data;
		}

		// Get user profile first to get the person URN
		$profile_url = 'https://api.linkedin.com/v2/me';
		$profile_data = $this->make_request( $profile_url, array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->credentials['access_token'],
			),
		) );

		if ( is_wp_error( $profile_data ) ) {
			return $profile_data;
		}

		if ( ! isset( $profile_data['id'] ) ) {
			return new WP_Error( 'profile_error', __( 'Failed to retrieve LinkedIn profile', 'social-feed-plugin' ) );
		}

		$person_urn = 'urn:li:person:' . $profile_data['id'];

		// Get posts
		$url = add_query_arg(
			array(
				'q' => 'author',
				'author' => $person_urn,
				'count' => 25,
			),
			'https://api.linkedin.com/v2/shares'
		);

		$data = $this->make_request( $url, array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->credentials['access_token'],
			),
		) );

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$normalized_data = $this->normalize_data( $data );
		$this->cache->set( $this->get_cache_key(), $normalized_data );

		return $normalized_data;
	}

	/**
	 * Normalize LinkedIn data.
	 *
	 * @param array $raw_data Raw LinkedIn API data.
	 * @return array Normalized data.
	 */
	protected function normalize_data( $raw_data ) {
		$posts = array();

		if ( ! isset( $raw_data['elements'] ) || ! is_array( $raw_data['elements'] ) ) {
			return $posts;
		}

		foreach ( $raw_data['elements'] as $item ) {
			$text = '';
			if ( isset( $item['text']['text'] ) ) {
				$text = sanitize_textarea_field( $item['text']['text'] );
			}

			$image = '';
			$video = '';
			$type = 'text';

			// Check for media
			if ( isset( $item['content']['media']['url'] ) ) {
				$media_url = esc_url_raw( $item['content']['media']['url'] );
				if ( isset( $item['content']['media']['category'] ) ) {
					if ( $item['content']['media']['category'] === 'VIDEO' ) {
						$video = $media_url;
						$type = 'video';
					} else {
						$image = $media_url;
						$type = 'image';
					}
				} else {
					$image = $media_url;
					$type = 'image';
				}
			}

			$share_id = isset( $item['id'] ) ? sanitize_text_field( $item['id'] ) : '';
			$link = $share_id ? 'https://www.linkedin.com/feed/update/' . $share_id : '';

			$posts[] = array(
				'id' => $share_id,
				'platform' => 'linkedin',
				'text' => $text,
				'created_time' => isset( $item['created']['time'] ) ? intval( $item['created']['time'] / 1000 ) : time(),
				'image' => $image,
				'video' => $video,
				'link' => esc_url_raw( $link ),
				'type' => $type,
			);
		}

		return $posts;
	}
}
