<?php
/**
 * Cache management for API responses
 *
 * @package    Social_Feed_Plugin
 * @subpackage Social_Feed_Plugin/includes
 */

class Social_Feed_Cache {

	/**
	 * Cache directory path.
	 *
	 * @var string
	 */
	private $cache_dir;

	/**
	 * Cache duration in seconds.
	 *
	 * @var int
	 */
	private $cache_duration;

	/**
	 * Initialize the class.
	 */
	public function __construct() {
		$upload_dir = wp_upload_dir();
		$this->cache_dir = $upload_dir['basedir'] . '/social-feed-cache';
		
		$options = get_option( 'social_feed_plugin_options' );
		$this->cache_duration = isset( $options['cache_duration'] ) ? intval( $options['cache_duration'] ) : 3600;
	}

	/**
	 * Get cached data.
	 *
	 * @param string $key Cache key.
	 * @return mixed|false Cached data or false if not found or expired.
	 */
	public function get( $key ) {
		$cache_file = $this->get_cache_file( $key );

		if ( ! file_exists( $cache_file ) ) {
			return false;
		}

		$cache_time = filemtime( $cache_file );
		if ( ( time() - $cache_time ) > $this->cache_duration ) {
			unlink( $cache_file );
			return false;
		}

		$data = file_get_contents( $cache_file );
		return maybe_unserialize( $data );
	}

	/**
	 * Set cached data.
	 *
	 * @param string $key Cache key.
	 * @param mixed  $data Data to cache.
	 * @return bool Success.
	 */
	public function set( $key, $data ) {
		if ( ! file_exists( $this->cache_dir ) ) {
			wp_mkdir_p( $this->cache_dir );
		}

		$cache_file = $this->get_cache_file( $key );
		$serialized_data = maybe_serialize( $data );
		
		return file_put_contents( $cache_file, $serialized_data ) !== false;
	}

	/**
	 * Delete cached data.
	 *
	 * @param string $key Cache key.
	 * @return bool Success.
	 */
	public function delete( $key ) {
		$cache_file = $this->get_cache_file( $key );
		
		if ( file_exists( $cache_file ) ) {
			return unlink( $cache_file );
		}
		
		return false;
	}

	/**
	 * Clear all cached data.
	 *
	 * @return bool Success.
	 */
	public function clear_all() {
		if ( ! file_exists( $this->cache_dir ) ) {
			return true;
		}

		$files = glob( $this->cache_dir . '/*.cache' );
		foreach ( $files as $file ) {
			if ( is_file( $file ) ) {
				unlink( $file );
			}
		}
		
		return true;
	}

	/**
	 * Get cache file path.
	 *
	 * @param string $key Cache key.
	 * @return string Cache file path.
	 */
	private function get_cache_file( $key ) {
		$hash = md5( $key );
		return $this->cache_dir . '/' . $hash . '.cache';
	}
}
