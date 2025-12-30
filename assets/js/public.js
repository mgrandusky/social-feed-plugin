/**
 * Public JavaScript for Social Feed Plugin
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		// Handle video playback for non-YouTube videos
		$('.social-feed-media video').on('play', function() {
			// Pause other videos when one starts playing
			$('.social-feed-media video').not(this).each(function() {
				this.pause();
			});
		});

		// YouTube lightbox functionality
		var $lightbox = $('#social-feed-youtube-lightbox');
		var $iframe = $('#social-feed-youtube-iframe');
		var $close = $('.social-feed-lightbox-close');
		var $overlay = $('.social-feed-lightbox-overlay');

		// Open lightbox when YouTube thumbnail is clicked
		$(document).on('click', '.social-feed-youtube-thumbnail', function(e) {
			e.preventDefault();
			var videoId = $(this).data('youtube-video-id');
			
			// Validate YouTube video ID format (11 characters: alphanumeric, hyphens, underscores)
			if (videoId && /^[a-zA-Z0-9_-]{11}$/.test(videoId)) {
				// Build YouTube embed URL with autoplay
				var embedUrl = 'https://www.youtube.com/embed/' + encodeURIComponent(videoId) + '?autoplay=1&rel=0';
				$iframe.attr('src', embedUrl);
				$lightbox.fadeIn(300);
				
				// Prevent body scroll when lightbox is open
				$('body').css('overflow', 'hidden');
			}
		});

		// Close lightbox
		function closeLightbox() {
			$lightbox.fadeOut(300, function() {
				$iframe.attr('src', ''); // Stop video playback
				$('body').css('overflow', '');
			});
		}

		$close.on('click', closeLightbox);
		$overlay.on('click', closeLightbox);

		// Close on ESC key
		$(document).on('keydown', function(e) {
			if (e.key === 'Escape' && $lightbox.is(':visible')) {
				closeLightbox();
			}
		});
	});

})(jQuery);
