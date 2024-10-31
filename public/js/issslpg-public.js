// Code was moved into shortcode

// (function( $ ) {
// 	'use strict';
//
// 	$(function() {
// 		setup_accordion();
// 	});
//
// 	/* FAQ Accordion
// 	 ========================================================================= */
//
// 	function setup_accordion() {
// 		var $all_triggers = $('.js-issslpg-accordion-trigger');
// 		var $all_targets = $('.js-issslpg-accordion-target');
//
// 		$all_triggers.click(function(e) {
// 			// If panel is closed...
// 			if( $(this).parent().attr('data-status') == 'closed' ) {
// 				// Close all panels
// 				$all_targets.each(function( index ) {
// 					$(this).parent().attr('data-status', 'closed');
// 					$(this).slideUp();
// 				});
// 				// Open panel
// 				$(this).next().slideDown();
// 				$(this).parent().attr('data-status', 'open');
// 			}
// 			// If panel is open...
// 			else {
// 				// Close panel
// 				$(this).next().slideUp();
// 				$(this).parent().attr('data-status', 'closed');
// 			}
//
// 			e.preventDefault();
// 		});
// 	}
//
// })( jQuery );
