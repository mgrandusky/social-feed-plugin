<?php
/**
 * YouTube API integration
 *
 * @package    Social_Feed_Plugin
 * @subpackage Social_Feed_Plugin/includes
 */

class Social_Feed_YouTube_API extends Social_Feed_API_Base {

	/**
	 * YouTube Data API version.
	 *
	 * @var string
	 */
	const API_VERSION = 'v3';

	/**
	 * Initialize the class.
	 */
	public function __construct() {
		$this->platform = 'youtube';
		parent::__construct();
	}

	/**
	 * Get YouTube API credentials.
	 *
	 * @return array
	 */
	protected function get_credentials() {
		$options = get_option( 'social_feed_plugin_options' );
		
		$credentials = array();
		if ( ! empty( $options['youtube_api_key'] ) ) {
			$credentials['api_key'] = sanitize_text_field( $options['youtube_api_key'] );
		}
		
		return $credentials;
	}

	/**
	 * Fetch YouTube feed.
	 *
	 * @return array|WP_Error
	 */
	public function fetch_feed() {
		if ( ! $this->is_configured() ) {
			return new WP_Error( 'not_configured', __( 'YouTube API is not configured', 'social-feed-plugin' ) );
		}

		// Check cache first
		$cached_data = $this->cache->get( $this->get_cache_key() );
		if ( $cached_data !== false ) {
			return $cached_data;
		}

		$options = get_option( 'social_feed_plugin_options' );
		$channel_id = ! empty( $options['youtube_channel_id'] ) ? sanitize_text_field( $options['youtube_channel_id'] ) : '';
		
		if ( empty( $channel_id ) ) {
			return new WP_Error( 'missing_channel_id', __( 'YouTube Channel ID is required', 'social-feed-plugin' ) );
		}

		// Get channel uploads playlist ID
		$channel_url = add_query_arg(
			array(
				'part' => 'contentDetails',
				'id' => $channel_id,
				'key' => $this->credentials['api_key'],
			),
			'https://www.googleapis.com/youtube/' . self::API_VERSION . '/channels'
		);

		$channel_data = $this->make_request( $channel_url );

		if ( is_wp_error( $channel_data ) ) {
			return $channel_data;
		}

		if ( ! isset( $channel_data['items'][0]['contentDetails']['relatedPlaylists']['uploads'] ) ) {
			return new WP_Error( 'channel_error', __( 'Failed to retrieve YouTube channel data', 'social-feed-plugin' ) );
		}

		$uploads_playlist_id = $channel_data['items'][0]['contentDetails']['relatedPlaylists']['uploads'];

		// Get videos from uploads playlist
		$url = add_query_arg(
			array(
				'part' => 'snippet,contentDetails',
				'playlistId' => $uploads_playlist_id,
				'maxResults' => 25,
				'key' => $this->credentials['api_key'],
			),
			'https://www.googleapis.com/youtube/' . self::API_VERSION . '/playlistItems'
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
	 * Normalize YouTube data.
	 *
	 * @param array $raw_data Raw YouTube API data.
	 * @return array Normalized data.
	 */
	protected function normalize_data( $raw_data ) {
		$posts = array();

		if ( ! isset( $raw_data['items'] ) || ! is_array( $raw_data['items'] ) ) {
			return $posts;
		}

		foreach ( $raw_data['items'] as $item ) {
			if ( ! isset( $item['snippet'] ) ) {
				continue;
			}

			$snippet = $item['snippet'];
			$video_id = isset( $snippet['resourceId']['videoId'] ) ? sanitize_text_field( $snippet['resourceId']['videoId'] ) : '';
			
			// Validate YouTube video ID format (11 characters: alphanumeric, hyphens, underscores)
			if ( empty( $video_id ) || ! preg_match( '/^[a-zA-Z0-9_-]{11}$/', $video_id ) ) {
				continue;
			}

			// Get thumbnail - prefer high quality
			$thumbnail = '';
			if ( isset( $snippet['thumbnails']['high']['url'] ) ) {
				$thumbnail = esc_url_raw( $snippet['thumbnails']['high']['url'] );
			} elseif ( isset( $snippet['thumbnails']['medium']['url'] ) ) {
				$thumbnail = esc_url_raw( $snippet['thumbnails']['medium']['url'] );
			} elseif ( isset( $snippet['thumbnails']['default']['url'] ) ) {
				$thumbnail = esc_url_raw( $snippet['thumbnails']['default']['url'] );
			}

			$posts[] = array(
				'id' => $video_id,
				'platform' => 'youtube',
				'text' => isset( $snippet['title'] ) ? sanitize_text_field( $snippet['title'] ) : '',
				'created_time' => isset( $snippet['publishedAt'] ) ? strtotime( $snippet['publishedAt'] ) : time(),
				'image' => $thumbnail,
				'video' => 'https://www.youtube.com/watch?v=' . $video_id,
				'link' => 'https://www.youtube.com/watch?v=' . $video_id,
				'type' => 'video',
			);
		}

		return $posts;
	}
}
