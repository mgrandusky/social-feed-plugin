/**
 * Public JavaScript for Social Feed Plugin
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		// Add any public-specific JavaScript functionality here
		
		// Example: Handle video playback
		$('.social-feed-media video').on('play', function() {
			// Pause other videos when one starts playing
			$('.social-feed-media video').not(this).each(function() {
				this.pause();
			});
		});
	});

})(jQuery);
