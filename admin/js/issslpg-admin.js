(function( $ ) {
	'use strict';

	$(function() {
		html_sitemap_export();
		ajax_update_download_queue();
		ajax_unit_downloader();
		ajax_unit_auto_downloader();
		custom_locations_interface();
		hide_freemius_menu_items_on_simulated_plan();
		hide_freemius_menu_items_when_plugin_is_white_labeled();
		increase_cmb2_wysiwyg_panel_size();
		confirmation_dialog_before_removing_cmb2_block();
		hide_lpg_panels_when_local_content_page_is_selected();
		setup_lightgallery();
	});

	/* LightGallery
	 ========================================================================= */

	function setup_lightgallery() {
		jQuery('.js-issslpg-lightgallery').lightGallery({
			videoMaxWidth: '1200px',
			selector: '.js-issslpg-lightgallery-item',
		});
	}

	/* HTML Sitemap export
	 ========================================================================= */

	function html_sitemap_export() {
		const $select = $('.js-issslpg-html-sitemap-export-select');
		const $button = $('.js-issslpg-html-sitemap-button');

		$select.change( function(e) {
			var template_page_id = $(this).children('option:selected').val();
			var url = new URL($button.attr('href'));
			var search_params = url.searchParams;
			search_params.set('export_html_sitemap_template', template_page_id)
			url.search = search_params.toString();
			var new_url = url.toString();
			$button.attr('href', new_url)
			console.log( new_url );
		} );
	}

	/* AJAX
	 ========================================================================= */

	function ajax_update_download_queue() {
		setTimeout(function() {
			jQuery.ajax({
				type: "post",
				dataType: "json",
				url: ajaxurl,
				data: {
					_nonce: issslpg_ajax_object.nonce,
					action: 'update_download_queue',
				},
				success: function(response) {
					ajax_update_download_queue();
				}
			});
		}, 3000);
	}

	/* AJAX
	 ========================================================================= */

	function ajax_unit_auto_downloader() {
		const pending_unit_ids = issslpg_ajax_object.pending_unit_ids;
		const pending_unit_categories = issslpg_ajax_object.pending_unit_categories;

		if ( pending_unit_categories == '' || pending_unit_ids.length < 1 ) {
			return false;
		}

		setTimeout(function(){
			const unit_id = pending_unit_ids[0];
			const unit_categories = pending_unit_categories;

			jQuery.ajax({
				type: "post",
				dataType: "json",
				url: ajaxurl,
				data: {
					_nonce: issslpg_ajax_object.nonce,
					action: 'download_remote_unit',
					unit_categories: unit_categories,
					unit_id: unit_id,
				},
				success: function(response) {
					console.log(response);
					if(response.status == "processing") {
						ajax_unit_auto_downloader();
						update_progress_bar( response.progress, unit_id );
					}
					else if(response.status == "done") {
						issslpg_ajax_object.pending_unit_ids.splice( $.inArray(unit_id, issslpg_ajax_object.pending_unit_ids), 1 );
						update_progress_bar( response.progress, unit_id );
						update_button( response.status, unit_id );
						ajax_unit_auto_downloader();
						// update_icon( response.status, unit_id );
					}
					else {
						update_progress_bar( response.progress, unit_id, true );
						// Swal.fire({
						// 	title: 'Error',
						// 	text: 'The data could not be downloaded due to an error.',
						// 	icon: 'error',
						// 	confirmButtonColor: '#3182ce',
						// 	confirmButtonText: 'OK',
						// });
						console.log( 'ISSSLPG: Error while downloading remote state data.' );
					}
				}
			});
		}, 5000);
	}

	/* AJAX
	 ========================================================================= */

	function ajax_unit_downloader() {

		jQuery(".js-issslpg-state-download-button").click( function(e) {
			const unit_id = jQuery(this).attr("data-unit-id");
			const unit_categories = jQuery(this).attr("data-unit-category");
			const location_name = jQuery(this).attr("data-location-name");
			const download_status = jQuery(this).attr("data-download-status");
			const download_progress = jQuery(this).attr("data-download-progress");
			const api_status = jQuery(this).attr("data-api-status");
			e.preventDefault();

			if ( ajax_validate_api_status(api_status) && 'pending' == download_status && unit_categories && unit_id && location_name ) {
				Swal.fire({
					title: 'Download ' + location_name + '?',
					text: 'Start downloading location data for ' + location_name + '',
					icon: 'question',
					showCancelButton: true,
					confirmButtonColor: '#3182ce',
					cancelButtonColor: '#aaa',
					confirmButtonText: 'Download'
				}).then((result) => {
					if (result.value) {
						console.log( 'ISSSLPG: Request remote data download for state with ID ' + unit_id );
						update_progress_bar( download_progress, unit_id );
						ajax_download_unit( unit_categories, unit_id );
					}
				});
			}
		});
	}

	function ajax_validate_api_status( api_status ) {
		if ( api_status == 'no-connection' ) {
			Swal.fire({
				title: 'Error',
				text: 'There was a problem connecting to the API. Please update the plugin to the newest version or wait some time until we fix the issue.',
				icon: 'error',
				confirmButtonColor: '#3182ce',
				confirmButtonText: 'OK',
			});
			return false;
		} else if ( api_status == 'out-of-date' ) {
			Swal.fire({
				title: 'Error',
				text: 'Please update the plugin to the newest version to be able to download data.',
				icon: 'error',
				confirmButtonColor: '#3182ce',
				confirmButtonText: 'OK',
			});
			return false;
		}

		return true;
	}

	function ajax_download_unit( unit_categories, unit_id ) {
		jQuery.ajax({
			type: "post",
			dataType: "json",
			url: ajaxurl,
			data: {
				_nonce: issslpg_ajax_object.nonce,
				action: 'download_remote_unit',
				unit_categories: unit_categories,
				unit_id: unit_id,
			},
			success: function(response) {
				console.log(response);
				if(response.status == "processing") {
					ajax_download_unit( unit_categories, unit_id );
					update_progress_bar( response.progress, unit_id );
				}
				else if(response.status == "done") {
					update_progress_bar( response.progress, unit_id );
					update_button( response.status, unit_id );
					// update_icon( response.status, unit_id );
				}
				else {
					update_progress_bar( response.progress, unit_id, true );
					Swal.fire({
						title: 'Error',
						text: 'The data could not be downloaded due to an error.',
						icon: 'error',
						confirmButtonColor: '#3182ce',
						confirmButtonText: 'OK',
					});
					console.log( 'ISSSLPG: Error while downloading remote state data.' );
				}
			}
		});
	}

	function update_button( status, unit_id ) {
		const $link_wrapper = jQuery('.js-issslpg-state-link-wrapper[data-unit-id="'+unit_id+'"]');
		// const $link = jQuery('.js-issslpg-state-link[data-unit-id="'+unit_id+'"]');
		const $download_button = jQuery('.issslpg-state-download-button[data-unit-id="'+unit_id+'"]');
		if ( ! $link_wrapper.length ) {
			return false;
		}
		if(status == "done") {
			$link_wrapper.addClass('issslpg-show');
			$link_wrapper.removeClass('issslpg-hide');
			$download_button.addClass('issslpg-hide');
			$download_button.removeClass('issslpg-show');
		}
	}

	function update_icon( status, unit_id ) {
		const $icon = jQuery('.js-issslpg-state-download-icon[data-unit-id="'+unit_id+'"]');
		if ( ! $icon.length ) {
			return false;
		}
		if ( status == 'done' ) {
			$icon.removeClass('dashicons-download');
			$icon.addClass('dashicons-arrow-right-alt');
		} else {
			$icon.removeClass('dashicons-arrow-right-alt');
			$icon.addClass('dashicons-download');
		}
	}

	function update_progress_bar( progress, unit_id, error = false ) {
		const $progress_bar = jQuery('.js-issslpg-progress-bar[data-unit-id="'+unit_id+'"]');
		if ( ! $progress_bar.length ) {
			return false;
		}
		const $progress_bar_status = jQuery('.js-issslpg-progress-bar-status', $progress_bar);
		if ( ! $progress_bar_status.length ) {
			return false;
		}
		if ( error ) {
			$progress_bar.addClass('issslpg-hide');
			return false;
		}
		// if ( 'true' == $progress_bar.attr('data-active') ) {
		$progress_bar.attr('data-progress', progress);
		$progress_bar.attr('data-active', 'true');
		$progress_bar_status.css( 'width', progress + '%' );
		// }
		if ( progress == 100 ) {
			$progress_bar.attr('data-active', 'false');
			$progress_bar.attr('data-status', 'done');
		} else {
			$progress_bar.removeClass('issslpg-hide');
		}
	}

	/* Custom Locations Interface
	 ========================================================================= */

	function custom_locations_interface() {
		var $interface_area = $('.js-isssplg-custom-location-area');
		var $input_area_template = $('.js-isssplg-custom-location-input-area-template');

		//set_data();
		add_initial_input_area();

		// Add Button
		$('.js-isssplg-custom-location-area').on( 'click', '.js-isssplg-custom-location-add-button', function() {
			add_input_area();
			set_data();
		} );

		// Remove Button
		$('.js-isssplg-custom-location-area').on( 'click', '.js-isssplg-custom-location-remove-button', function() {
			var $input_area = $(this).parents('.js-isssplg-custom-location-input-area');
			$('.js-issslpg-custom-location-method', $input_area).val('remove');
			$input_area.addClass('is-removed');
			$input_area.hide();
			//$input_area.slideUp( "slow", function() {
			//	$input_area.remove();
			//});
			set_data();
			add_initial_input_area();
		} );

		$('.js-isssplg-custom-location-area').on( 'keyup', '.isssplg-custom-location-input-wrapper input', function() {
			set_data();
		} );

		function add_initial_input_area() {
			var $first_input_area = $('.isssplg-custom-location-input-area:not(.is-removed):not(.isssplg-custom-location-input-area--template)');
			if ( $first_input_area.is(":hidden") || ! $first_input_area.length ) {
				add_input_area();
			}
		}

		function add_input_area() {
			var $new_input_area = $input_area_template.clone();
			$new_input_area.appendTo($interface_area);
			$new_input_area.removeClass('isssplg-custom-location-input-area--template');
			$new_input_area.removeClass('js-isssplg-custom-location-input-area-template');
			var hash = Math.random().toString(36).substr(2);
			$('.js-issslpg-custom-location-hash', $new_input_area).val(hash);
			$('.js-issslpg-custom-location-method', $new_input_area).val('add');
		}

		function set_data() {
			var $export_button = $('.js-isssplg-custom-location-export-button');
			var $textarea = $('.js-isssplg-custom-location-data');
			var data = [];
			$('.isssplg-custom-location-input-area:not(.isssplg-custom-location-input-area--template)').each(function() {
				var data_item = {
					'hash':      $('input.issslpg-custom-location-hash', $(this)).val(),
					'method':    $('input.issslpg-custom-location-method', $(this)).val(),
					'name':      $('input.issslpg-custom-location-name', $(this)).val(),
					'zip_codes': $('input.issslpg-custom-location-zip-codes', $(this)).val(),
					'phone':     $('input.issslpg-custom-location-phone', $(this)).val(),
				};
				if(data_item['zip_codes']) {
					data_item['zip_codes'] = data_item['zip_codes'].split(",").map(function(zip_code) {
						return zip_code.trim() ? zip_code.trim() : null;
					});
				}
				if(data_item['name']) {
					data.push(data_item);
				}
			} );
			$textarea.text(JSON.stringify(data));
			$export_button.css('opacity', '.5');
			$export_button.css('cursor', 'not-allowed');
			$export_button.click(function(e) {
				e.preventDefault();
			});
			//$textarea.text(JSON.stringify(data).replace(/\"/g, ""));
		}
	}

	/* Hide all LPG panels, when local content page is selected
	 ========================================================================= */

	function hide_lpg_panels_when_local_content_page_is_selected() {
		var $selector = $('#_issslpg_local_content_page', '.post-type-issslpg-landing-page');

		toggle_lpg_panels($selector);

		$selector.change(function() {
			toggle_lpg_panels($selector);
		});

		function toggle_lpg_panels(selector) {
			var $lpg_panels = $('.cmb2-postbox:not(#issslpg_local_content_selector)', '.post-type-issslpg-landing-page');
			if( selector.val() != '' ) {
				$lpg_panels.hide();
			} else {
				$lpg_panels.show();
			}
		}
	}

	/* Display a confirmation dialog when a user clicks the button to remove
	 * a CMB2 block
	 ========================================================================= */

	function confirmation_dialog_before_removing_cmb2_block() {
		confirmation_dialog( $('.cmb-remove-group-row-button', '.post-type-issslpg-landing-page') );
		confirmation_dialog( $('.cmb-remove-group-row-button', '.post-type-issslpg-local') );

		function confirmation_dialog( button ) {
			button.click(function (e) {
				var ok = confirm('Are you sure you want to remove this item?');
				if (!ok) {
					e.preventDefault();
					return false;
				}
			});
		}
	}

	/* Hide Freemius Menu Items on Simulated Plan
	 * We have to do this through JS, because Freemius doesn't use the WP API to
	 * register sub menu pages
	 ========================================================================= */

	function hide_freemius_menu_items_on_simulated_plan() {
		if ( $('body').hasClass('js-issslpg-simulated-plan') ) {
			$( '.fs-submenu-item', '#toplevel_page_issslpg_location_settings').parent().parent().remove();
		}
	}

	/* Hide Freemius Menu Items when Plugin is White Labeled
	 * We have to do this through JS, because Freemius doesn't use the WP API to
	 * register sub menu pages
	 ========================================================================= */

	function hide_freemius_menu_items_when_plugin_is_white_labeled() {
		if ( $('body').hasClass('js-issslpg-white-labeled') ) {
			$( '.fs-submenu-item', '#toplevel_page_issslpg_location_settings').parent().parent().remove();
		}
	}

	/* Increase CMB2 WYSIWYG Panel size
	 ========================================================================= */

	function increase_cmb2_wysiwyg_panel_size() {
		$( '.cmbhandle-title', '.post-type-issslpg-landing-page' ).on( "click", function() {
			$('.mce-edit-area iframe').height(400);
		});
	}

})( jQuery );
