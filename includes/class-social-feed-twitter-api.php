<?php
/**
 * Twitter API integration
 *
 * @package    Social_Feed_Plugin
 * @subpackage Social_Feed_Plugin/includes
 */

class Social_Feed_Twitter_API extends Social_Feed_API_Base {

	/**
	 * Initialize the class.
	 */
	public function __construct() {
		$this->platform = 'twitter';
		parent::__construct();
	}

	/**
	 * Get Twitter API credentials.
	 *
	 * @return array
	 */
	protected function get_credentials() {
		$options = get_option( 'social_feed_plugin_options' );
		
		$credentials = array();
		if ( ! empty( $options['twitter_bearer_token'] ) ) {
			$credentials['bearer_token'] = sanitize_text_field( $options['twitter_bearer_token'] );
		}
		
		return $credentials;
	}

	/**
	 * Fetch Twitter feed.
	 *
	 * @return array|WP_Error
	 */
	public function fetch_feed() {
		if ( ! $this->is_configured() ) {
			return new WP_Error( 'not_configured', __( 'Twitter API is not configured', 'social-feed-plugin' ) );
		}

		// Check cache first
		$cached_data = $this->cache->get( $this->get_cache_key() );
		if ( $cached_data !== false ) {
			return $cached_data;
		}

		$options = get_option( 'social_feed_plugin_options' );
		$username = ! empty( $options['twitter_username'] ) ? sanitize_text_field( $options['twitter_username'] ) : '';
		
		if ( empty( $username ) ) {
			return new WP_Error( 'missing_username', __( 'Twitter username is required', 'social-feed-plugin' ) );
		}

		// First, get user ID
		$user_url = 'https://api.twitter.com/2/users/by/username/' . $username;
		$user_data = $this->make_request( $user_url, array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->credentials['bearer_token'],
			),
		) );

		if ( is_wp_error( $user_data ) ) {
			return $user_data;
		}

		if ( ! isset( $user_data['data']['id'] ) ) {
			return new WP_Error( 'user_not_found', __( 'Twitter user not found', 'social-feed-plugin' ) );
		}

		$user_id = $user_data['data']['id'];

		// Get user tweets
		$url = add_query_arg(
			array(
				'max_results' => 25,
				'tweet.fields' => 'created_at,entities,attachments',
				'expansions' => 'attachments.media_keys',
				'media.fields' => 'url,preview_image_url,type',
			),
			'https://api.twitter.com/2/users/' . $user_id . '/tweets'
		);

		$data = $this->make_request( $url, array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->credentials['bearer_token'],
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
	 * Normalize Twitter data.
	 *
	 * @param array $raw_data Raw Twitter API data.
	 * @return array Normalized data.
	 */
	protected function normalize_data( $raw_data ) {
		$posts = array();

		if ( ! isset( $raw_data['data'] ) || ! is_array( $raw_data['data'] ) ) {
			return $posts;
		}

		$media_lookup = array();
		if ( isset( $raw_data['includes']['media'] ) ) {
			foreach ( $raw_data['includes']['media'] as $media ) {
				$media_lookup[ $media['media_key'] ] = $media;
			}
		}

		$options = get_option( 'social_feed_plugin_options' );
		$username = ! empty( $options['twitter_username'] ) ? sanitize_text_field( $options['twitter_username'] ) : '';

		foreach ( $raw_data['data'] as $item ) {
			$image = '';
			$video = '';
			$type = 'text';

			if ( isset( $item['attachments']['media_keys'] ) ) {
				foreach ( $item['attachments']['media_keys'] as $media_key ) {
					if ( isset( $media_lookup[ $media_key ] ) ) {
						$media = $media_lookup[ $media_key ];
						if ( $media['type'] === 'photo' && ! empty( $media['url'] ) ) {
							$image = esc_url_raw( $media['url'] );
							$type = 'photo';
						} elseif ( in_array( $media['type'], array( 'video', 'animated_gif' ), true ) ) {
							$video = isset( $media['url'] ) ? esc_url_raw( $media['url'] ) : '';
							$image = isset( $media['preview_image_url'] ) ? esc_url_raw( $media['preview_image_url'] ) : '';
							$type = 'video';
						}
						break;
					}
				}
			}

			$tweet_id = isset( $item['id'] ) ? sanitize_text_field( $item['id'] ) : '';
			$link = $username ? 'https://twitter.com/' . $username . '/status/' . $tweet_id : '';

			$posts[] = array(
				'id' => $tweet_id,
				'platform' => 'twitter',
				'text' => isset( $item['text'] ) ? sanitize_textarea_field( $item['text'] ) : '',
				'created_time' => isset( $item['created_at'] ) ? strtotime( $item['created_at'] ) : time(),
				'image' => $image,
				'video' => $video,
				'link' => esc_url_raw( $link ),
				'type' => $type,
			);
		}

		return $posts;
	}
}
