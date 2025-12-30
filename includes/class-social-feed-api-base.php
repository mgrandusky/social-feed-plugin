<?php
/**
 * Base class for social media API integrations
 *
 * @package    Social_Feed_Plugin
 * @subpackage Social_Feed_Plugin/includes
 */

abstract class Social_Feed_API_Base {

	/**
	 * Cache instance.
	 *
	 * @var Social_Feed_Cache
	 */
	protected $cache;

	/**
	 * API credentials.
	 *
	 * @var array
	 */
	protected $credentials;

	/**
	 * Platform name.
	 *
	 * @var string
	 */
	protected $platform;

	/**
	 * Initialize the class.
	 */
	public function __construct() {
		$this->cache = new Social_Feed_Cache();
		$this->credentials = $this->get_credentials();
	}

	/**
	 * Get API credentials from options.
	 *
	 * @return array
	 */
	abstract protected function get_credentials();

	/**
	 * Fetch feed from API.
	 *
	 * @return array|WP_Error
	 */
	abstract public function fetch_feed();

	/**
	 * Make HTTP request with error handling.
	 *
	 * @param string $url API endpoint URL.
	 * @param array  $args Request arguments.
	 * @return array|WP_Error
	 */
	protected function make_request( $url, $args = array() ) {
		$response = wp_remote_get( $url, $args );

		if ( is_wp_error( $response ) ) {
			return new WP_Error(
				'api_error',
				sprintf(
					/* translators: %s: error message */
					__( 'API request failed: %s', 'social-feed-plugin' ),
					$response->get_error_message()
				)
			);
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );
		
		if ( $status_code !== 200 ) {
			// Try to get error details from response body
			$error_message = sprintf(
				/* translators: %d: HTTP status code */
				__( 'API returned error code: %d', 'social-feed-plugin' ),
				$status_code
			);
			
			// Attempt to parse error details from JSON response
			$error_data = json_decode( $body, true );
			if ( json_last_error() === JSON_ERROR_NONE && isset( $error_data['errors'] ) ) {
				if ( is_array( $error_data['errors'] ) && ! empty( $error_data['errors'] ) ) {
					$error_details = array();
					foreach ( $error_data['errors'] as $error ) {
						if ( isset( $error['message'] ) ) {
							$error_details[] = $error['message'];
						} elseif ( is_string( $error ) ) {
							$error_details[] = $error;
						}
					}
					if ( ! empty( $error_details ) ) {
						$error_message .= ': ' . implode( ', ', $error_details );
					}
				}
			} elseif ( json_last_error() === JSON_ERROR_NONE && isset( $error_data['error'] ) ) {
				// Handle single error object
				if ( is_array( $error_data['error'] ) && isset( $error_data['error']['message'] ) ) {
					$error_message .= ': ' . $error_data['error']['message'];
				} elseif ( is_string( $error_data['error'] ) ) {
					$error_message .= ': ' . $error_data['error'];
				}
			} elseif ( json_last_error() === JSON_ERROR_NONE && isset( $error_data['message'] ) ) {
				// Handle message field directly
				$error_message .= ': ' . $error_data['message'];
			}
			
			return new WP_Error( 'api_error', $error_message );
		}

		$data = json_decode( $body, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return new WP_Error(
				'json_error',
				__( 'Failed to parse API response', 'social-feed-plugin' )
			);
		}

		return $data;
	}

	/**
	 * Normalize feed data to standard format.
	 *
	 * @param array $raw_data Raw API data.
	 * @return array Normalized data.
	 */
	abstract protected function normalize_data( $raw_data );

	/**
	 * Check if API is properly configured.
	 *
	 * @return bool
	 */
	public function is_configured() {
		return ! empty( $this->credentials );
	}

	/**
	 * Get cache key for this platform.
	 *
	 * @return string
	 */
	protected function get_cache_key() {
		return 'social_feed_' . $this->platform;
	}
}
