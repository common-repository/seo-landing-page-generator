<?php

use WeDevs\ORM\WP\CityData as CityData;

class ISSSLPG_Landing_Page {

	private static $landing_page_id = false;

	static public function is_landing_page( $page_id = false ) {
		$page_id = $page_id ? $page_id : get_the_ID();
		return ( 'issslpg-landing-page' == get_post_type( $page_id ) );
	}

	static public function set_landing_page_id() {
		if ( self::is_landing_page() ) {
			self::$landing_page_id = get_the_ID();
		}
	}

	static public function get_landing_page_id( $use_local_content_page_id = true ) {
		$local_content_page_id = get_post_meta( self::$landing_page_id, '_issslpg_local_content_page', true );
		if ( $use_local_content_page_id && $local_content_page_id ) {
			return (int)$local_content_page_id;
		}
		return self::$landing_page_id;
	}

	static public function modify_content( $content ) {
		global $wp_embed;

		$template_page_id = get_post_meta( get_the_ID(), '_issslpg_template_page_id', true );

		$template_post = get_post($template_page_id);
		$content = $template_post->post_content;

		$content = $wp_embed->autoembed( $content );
		$content = $wp_embed->run_shortcode( $content );
//		$content = wpautop( $content );
		$content = do_shortcode( $content );

		// Render blocks
		// Source: https://florianbrinkmann.com/en/display-specific-gutenberg-blocks-of-a-post-outside-of-the-post-content-in-the-theme-5620/
		// Source: https://wordpress.stackexchange.com/questions/323759/get-blocks-from-other-pages-from-within-current-page
//		$blocks = parse_blocks( $content );
//		$content = '';
//		foreach ( $blocks as $block ) {
//			$content .= render_block( $block );
//		}

		// $template_page = new WP_Query( array(
		// 	'page_id'   => $template_page_id,
		// 	'post_type' => 'issslpg-template',
		// ) );
		// if ( $template_page->have_posts() ) {
		// 	while ( $template_page->have_posts() ) {
		// 		$template_page->the_post();
		// 		$content = get_the_content();
		// 		// Let's run the filter, because we're using it to modify
		// 		// content with SEO Content Randomizer plugin
		// 		$content = apply_filters( 'the_content', $content );
		// 		$content = str_replace( ']]>', ']]>', $content );

		// 		// Process Content
		// 		// $content = $wp_embed->autoembed( $content );
		// 		// $content = $wp_embed->run_shortcode( $content );
		// 		// $content = wpautop( $content );
		// 		// $content = do_shortcode( $content );
		// 	}
		// 	wp_reset_postdata();
		// }

		return $content;
	}

	static public function get_page_id_by_custom_location_hash_and_template_page_id( $custom_location_hash, $template_page_id ) {
		$page_id = false;

		$landing_pages = new WP_Query( array(
				'post_type'   => 'issslpg-landing-page',
				'post_status' => array( 'publish', 'trash' ),
				'meta_query'  => array(
						'relation' => 'AND',
						array(
								'key'     => '_issslpg_template_page_id',
								'value'   => (string) $template_page_id,
								'compare' => '='
						),
						array(
								'key'     => '_issslpg_location_hash',
								'value'   => (string) $custom_location_hash,
								'compare' => '='
						),
				),
		) );
		while ( $landing_pages->have_posts() ) :
			$landing_pages->the_post();
			$page_id = get_the_ID();
		endwhile;
		wp_reset_postdata();

		return $page_id;
	}

	static public function get_page_id_by_city_and_template_page_id( $city_id, $template_page_id ) {
		$page_id = false;

		$landing_pages = new WP_Query( array(
			'post_type'   => 'issslpg-landing-page',
			'post_status' => array( 'publish', 'trash' ),
			'meta_query'  => array(
				'relation' => 'AND',
				array(
					'key'     => '_issslpg_template_page_id',
					'value'   => (string) $template_page_id,
					'compare' => '='
				),
				array(
					'key'     => '_issslpg_city_id',
					'value'   => (string) $city_id,
					'compare' => '='
				),
			),
		) );
		while ( $landing_pages->have_posts() ) :
			$landing_pages->the_post();
			$page_id = get_the_ID();
		endwhile;
		wp_reset_postdata();

		return $page_id;
	}

	static public function landing_page_exist( $city_id, $template_page_id ) {

		$landing_pages = new WP_Query( array(
			'post_type'   => 'issslpg-landing-page',
			'post_status' => array( 'publish', 'trash' ),
			'meta_query'  => array(
				'relation' => 'AND',
				array(
					'key'     => '_issslpg_template_page_id',
					'value'   => (string) $template_page_id,
					'compare' => '='
				),
				array(
					'key'     => '_issslpg_city_id',
					'value'   => (string) $city_id,
					'compare' => '='
				),
			),
		) );

		$landing_page_exist = $landing_pages->have_posts();
		wp_reset_postdata();

		return $landing_page_exist;
	}

	static public function create_or_update_correlating_landing_pages_by_template_page( $template_page_id ) {

		global $wpdb;

		// Cities
		$results = $wpdb->get_results( "
			SELECT cd.city_id, cc.county_id
			FROM {$wpdb->prefix}issslpg_city_data cd, {$wpdb->prefix}issslpg_city_county cc
			WHERE cd.active = 1 AND cc.city_id = cd.city_id
		" );
		foreach ( $results as $result ) {
			ISSSLPG_Admin_Scheduled_Tasks::add_landing_pages_to_update( $result->city_id, $result->county_id, $template_page_id );
		}
		ISSSLPG_Logger::log( 'Scheduled landing pages to update for template page "' . get_the_title( $template_page_id ), __METHOD__ );

		// Custom Locations
		$results = $wpdb->get_results( "
			SELECT cd.county_id, cd.custom_locations
			FROM {$wpdb->prefix}issslpg_county_data cd
			WHERE cd.custom_locations IS NOT NULL
		" );
		foreach ( $results as $result ) {
			$custom_locations = unserialize( $result->custom_locations );
			if ( ! empty( $custom_locations ) && is_array( $custom_locations ) ) {
				foreach ( $custom_locations as $custom_location ) {
					self::create_or_update_correlating_landing_pages_by_custom_locations( $custom_location, $result->county_id, $template_page_id );
				}
			}
		}

	}

	static public function get_title( $type, $template_page_id, $atts = array() ) {
		$template_page_title = get_the_title( $template_page_id );
		if ( 'heading' == $type ) {
			$title = sanitize_text_field( ISSSLPG_Options::get_setting( 'landing_page_heading_format' ) );
			if ( empty( $title ) ) {
				$title = "{$template_page_title} in {$atts['city']}, {$atts['state']}, {$atts['zip_code']}, {$atts['phone']}";
			}
		}
		else {
			$title = sanitize_text_field( ISSSLPG_Options::get_setting( 'landing_page_page_title_format' ) );
			if ( empty( $title ) ) {
				$title = "{$template_page_title} in {$atts['city']}, {$atts['state']}";
			}
		}

		preg_match_all( '/\[([^#]+?)\]/', $title, $placeholders_in_title );

		if ( in_array( 'title',      $placeholders_in_title[1] ) ) $title = str_replace( '[title]',      $template_page_title,     $title );
		if ( in_array( 'city',       $placeholders_in_title[1] ) ) $title = str_replace( '[city]',       $atts['city'],            $title );
		if ( in_array( 'state',      $placeholders_in_title[1] ) ) $title = str_replace( '[state]',      $atts['state'],           $title );
		if ( in_array( 'state_abbr', $placeholders_in_title[1] ) ) $title = str_replace( '[state_abbr]', $atts['state_abbr'],      $title );
		if ( in_array( 'county',     $placeholders_in_title[1] ) ) $title = str_replace( '[county]',     $atts['county'],          $title );
		if ( in_array( 'phone',      $placeholders_in_title[1] ) ) $title = str_replace( '[phone]',      $atts['phone'],           $title );
		if ( in_array( 'zip_code',   $placeholders_in_title[1] ) ) $title = str_replace( '[zip_code]',   $atts['zip_code'],        $title );

		return $title;
	}

	static public function remove_custom_location_landing_pages( $template_page_id, $custom_location_hash ) {
		$landing_pages = new WP_Query( array(
				'post_type'   => 'issslpg-landing-page',
				'post_status' => 'publish',
				'meta_query'  => array(
					'relation' => 'AND',
					array(
							'key'     => '_issslpg_template_page_id',
							'value'   => (string) $template_page_id,
							'compare' => '='
					),
					array(
							'key'     => '_issslpg_location_hash',
							'value'   => $custom_location_hash,
							'compare' => '='
					),
				),
		) );

		while ( $landing_pages->have_posts() ) :
			$landing_pages->the_post();
			wp_trash_post( get_the_ID() );
			ISSSLPG_Logger::log( 'Trashed custom location landing page:' . get_the_title(), __METHOD__ );
		endwhile;
		wp_reset_postdata();
	}

	static public function create_or_update_correlating_landing_pages_by_custom_locations( $custom_location, $county_handler, $template_page_id = false ) {
		require_once( ABSPATH . 'wp-admin/includes/post.php' );

		global $wpdb;

		$landing_pages = array();
//		$county_handler = get_post_meta( $template_page_id, '_issslpg_county_id', true );
		$county_data = new ISSSLPG_County_Data( $county_handler );

		$template_page_query_args = array(
			'post_type'      => 'issslpg-template',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);

		if ( $template_page_id ) {
			$template_page_id_args = array(
				'p' => $template_page_id,
			);
			$template_page_query_args = array_merge( $template_page_query_args, $template_page_id_args );
		}

		$template_pages = new WP_Query( $template_page_query_args );

		while ( $template_pages->have_posts() ) :
			$template_pages->the_post();

			$template_page_title = get_the_title();
			$template_page_id    = get_the_ID();

			$title_atts = array(
				'city'       => $custom_location['name'],
				'state'      => $county_data->state,
				'state_abbr' => $county_data->state_abbr,
				'county'     => $county_data->name,
				'phone'      => empty( $custom_location['phone'] ) ? $county_data->inherited_phone : $custom_location['phone'],
				'zip_code'   => empty( $custom_location['zip_codes'] ) ? '' : $custom_location['zip_codes'][0],
			);

			$title      = self::get_title( 'heading', $template_page_id, $title_atts );
			$page_title = self::get_title( 'page-title', $template_page_id, $title_atts );

			$slug = sanitize_title( "{$template_page_title} {$custom_location['name']} {$county_data->state}" );
			if ( ISSSLPG_Options::get_setting( 'use_old_landing_page_slug_format', false, 'iss_debug_settings' ) ) {
				$slug = sanitize_title( "{$template_page_title} in {$custom_location['name']} {$county_data->state}" );
			}

			// Since we changed the slugs in the new version, we have to account
			// for legacy slug, and keep using them for existing posts that are
			// just being updated.
			if ( ! function_exists( 'post_exists' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/post.php' );
			}
			if ( $existing_post_id = post_exists( $title ) ) {
				$existing_post = get_post($existing_post_id);
				$slug = $existing_post->post_name;
			}

			$landing_pages[$template_page_id]['title']               = $title;
			$landing_pages[$template_page_id]['page_title']          = $page_title;
			$landing_pages[$template_page_id]['template_page_title'] = $template_page_title;
			$landing_pages[$template_page_id]['slug']                = $slug;
			$landing_pages[$template_page_id]['template_page_id']    = $template_page_id;
			$landing_pages[$template_page_id]['location_name']       = $custom_location['name'];
			$landing_pages[$template_page_id]['location_hash']       = $custom_location['hash'];
		endwhile;
		wp_reset_postdata();

		foreach ( $landing_pages as $landing_page ) {
			$atts = array(
					'post_title' => $landing_page['title'],
					'post_name'  => $landing_page['slug'],
					'post_type'  => 'issslpg-landing-page',
					'meta_input' => array(
							'_issslpg_county_id'        => $county_data->id,
							'_issslpg_template_page_id' => $landing_page['template_page_id'],
							'_issslpg_page_title'       => $landing_page['page_title'],
							'_issslpg_location_name'    => $landing_page['location_name'],
							'_issslpg_location_hash'    => $landing_page['location_hash'],
					)
			);

			$page_id = self::get_page_id_by_custom_location_hash_and_template_page_id( $landing_page['location_hash'], $landing_page['template_page_id'] );

			// Don't create landing page if template page is excluded for this county
			$lp_template_page_id = $landing_page['template_page_id'];
			$result = $wpdb->get_row( "
				SELECT *
				FROM {$wpdb->prefix}issslpg_excluded_county_template_pages
				WHERE county_id = {$county_data->id}
				AND template_page_id = {$lp_template_page_id}
			" );

			$exclude_page = ( post_exists( $landing_page['title'] ) > 0 );
			if ( ! $exclude_page ) {
				$exclude_page = isset( $result );
			}

			if ( ! $exclude_page ) {
				if ( $page_id ) {
					$atts                = array_merge( $atts, array( 'ID' => $page_id ) ); // We need the ID to update the post
					$atts['post_status'] = get_post_status( $page_id );

					if ( ! wp_is_post_revision( $page_id ) ) {
						wp_update_post( $atts, true );
						ISSSLPG_Logger::log( "Updated custom location landing page: {$atts['post_title']}", __METHOD__ );
					}
				} elseif ( ! ISSSLPG_Helpers::is_county_limit_reached() || $county_data->status ) {
					$atts['post_status'] = 'publish';
					wp_insert_post( $atts, true );
					ISSSLPG_Logger::log( "Created custom location landing page: {$atts['post_title']}", __METHOD__ );
				}
			}
		}
	}

	static public function create_or_update_correlating_landing_pages_by_city( $city_handler, $county_handler = false, $template_page_id = false ) {

		global $wpdb;

		$landing_pages = array();
		$city_data     = new ISSSLPG_City_Data( $city_handler );

		$template_page_query_args = array(
			'post_type'      => 'issslpg-template',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);

		if ( $template_page_id ) {
			$template_page_id_args = array(
				'p' => $template_page_id,
			);
			$template_page_query_args = array_merge( $template_page_query_args, $template_page_id_args );
		}

		$template_pages = new WP_Query( $template_page_query_args );

		while ( $template_pages->have_posts() ) :
			$template_pages->the_post();

			$template_page_title = get_the_title();
			$template_page_id    = get_the_ID();

			$title_atts = array();
			$title_atts['city']       = $city_data->name;
			$title_atts['state']      = $city_data->state;
			$title_atts['state_abbr'] = $city_data->state_abbr;
			$title_atts['county']     = $city_data->county;
			$title_atts['phone']      = $city_data->inherited_phone;
			$title_atts['zip_code']   = $city_data->zip_code;

			$title       = self::get_title( 'heading', $template_page_id, $title_atts );
			$page_title  = self::get_title( 'page-title', $template_page_id, $title_atts );

			$slug = sanitize_title( "{$template_page_title} {$city_data->name} {$city_data->state}" );
			if ( ISSSLPG_Options::get_setting( 'use_old_landing_page_slug_format', false, 'iss_debug_settings' ) ) {
				$slug = sanitize_title( "{$template_page_title} in {$city_data->name} {$city_data->state}" );
			}

			// Since we changed the slugs in the new version, we have to account
			// for legacy slug, and keep using them for existing posts that are
			// just being updated.
			if ( ! function_exists( 'post_exists' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/post.php' );
			}
			if ( $existing_post_id = post_exists( $title ) ) {
				$existing_post = get_post($existing_post_id);
				$slug = $existing_post->post_name;
			}

			$landing_pages[$template_page_id]['title']               = $title;
			$landing_pages[$template_page_id]['page_title']          = $page_title;
			$landing_pages[$template_page_id]['template_page_title'] = $template_page_title;
			$landing_pages[$template_page_id]['slug']                = $slug;
			$landing_pages[$template_page_id]['template_page_id']    = $template_page_id;
			$landing_pages[$template_page_id]['city_id']             = $city_data->id;
		endwhile;
		wp_reset_postdata();


		foreach ( $landing_pages as $landing_page ) {
			$exclude_page = false;
			$atts = array(
				'post_title'  => $landing_page['title'],
				'post_name'   => $landing_page['slug'],
				'post_type'   => 'issslpg-landing-page',
				'meta_input'  => array(
					'_issslpg_template_page_id' => $landing_page['template_page_id'],
					'_issslpg_city_id'          => $landing_page['city_id'],
					'_issslpg_page_title'       => $landing_page['page_title'],
				)
			);

			$page_id = self::get_page_id_by_city_and_template_page_id( $landing_page['city_id'], $landing_page['template_page_id'] );

			if ( ! $county_handler ) {
				$county_handler = get_post_meta( $page_id, '_issslpg_county_id', true );
			}

			if ( $county_handler ) {
				$county_data = new ISSSLPG_County_Data( $county_handler );
				$atts['meta_input']['_issslpg_county_id'] = $county_data->id;

				// Don't create landing page if template page is excluded for this county
				$lp_template_page_id = $landing_page['template_page_id'];
				$result = $wpdb->get_row( "
					SELECT *
					FROM {$wpdb->prefix}issslpg_excluded_county_template_pages
					WHERE county_id = {$county_data->id}
					AND template_page_id = {$lp_template_page_id}
				" );
				$exclude_page = isset( $result );
//				if ( isset($result) ) {
//					error_log( "Exclude Page: {$atts['post_title']} ($lp_template_page_id)" );
//				}
			}
			if ( ! $exclude_page ) {
				if ( $page_id ) {
//					error_log( "Update Page: {$atts['post_title']} ($lp_template_page_id)" );
					$atts                = array_merge( $atts, array( 'ID' => $page_id ) ); // We need the ID to update the post
					$atts['post_status'] = get_post_status( $page_id );
//					$atts['post_status'] = 'publish';
//					if ( get_post_status( $page_id ) == 'trash' ) {
//						$atts['post_status'] = 'publish';
//						error_log( "Page in Trash: {$atts['post_title']} ($lp_template_page_id)" );
//					}
					//				if ( ! ISSSLPG_Template_Page::has_published_corralating_landing_pages( $landing_page['template_page_id'] ) ) {
					//					if ( $city_data->status ) {
					//						$atts['post_status'] = 'trash';
					//					} else {
					//						$atts['post_status'] = 'publish';
					//					}
					//				}
					if ( ! wp_is_post_revision( $page_id ) ) {
						wp_update_post( $atts, true );
						ISSSLPG_Logger::log( "Updated landing page: {$atts['post_title']}", __METHOD__ );
					}
				} elseif ( ! ISSSLPG_Helpers::is_county_limit_reached() || ( isset( $county_data->status ) && $county_data->status ) ) {
//					error_log( "Create Page: {$atts['post_title']} ($lp_template_page_id)" );
					$atts['post_status'] = 'publish';
					if ( ! post_exists( $atts['post_title'] ) ) {
						wp_insert_post( $atts, true );
						ISSSLPG_Logger::log( "Created landing page: {$atts['post_title']}", __METHOD__ );
					}
				}
			}
		}

	}

	static public function untrash_correlating_landing_pages_by_county_and_city( $county_id, $city_id ) {

		$excluded_template_pages = self::get_excluded_template_pages_by_county( $county_id );
		$excluded_template_page_ids = array();
		foreach( $excluded_template_pages as $excluded_template_page ) {
			$excluded_template_page_ids[] = $excluded_template_page->template_page_id;
		}

		$landing_pages = new WP_Query( array(
			'post_type'   => 'issslpg-landing-page',
			'post_status' => 'trash',
			'meta_query'  => array(
				array(
					'key'     => '_issslpg_county_id',
					'value'   => (string) $county_id,
					'compare' => '='
				),
				array(
					'key'     => '_issslpg_city_id',
					'value'   => (string) $city_id,
					'compare' => '='
				),
			),
		) );

		if ( $landing_pages->have_posts() ) {
			while( $landing_pages->have_posts() ) {
				$landing_pages->the_post();
				$template_page_id = get_post_meta( get_the_ID(), '_issslpg_template_page_id', true );
				if ( ! in_array( $template_page_id, $excluded_template_page_ids ) ) {
					wp_untrash_post( get_the_ID() );
					ISSSLPG_Logger::log( 'Untrashed landing page: ' . get_the_title(), __METHOD__ );
				}
			}
		}
		wp_reset_postdata();
	}

	static public function get_excluded_template_pages_by_county( $county_id ) {
		global $wpdb;

		return $wpdb->get_results( "
					SELECT *
					FROM {$wpdb->prefix}issslpg_excluded_county_template_pages
					WHERE county_id = {$county_id}
				" );
	}

	static public function delete_excluded_landing_pages_by_county( $county_id ) {

		$results = self::get_excluded_template_pages_by_county( $county_id );

		foreach( $results as $result ) {
			$landing_pages = new WP_Query( array(
				'post_type'      => 'issslpg-landing-page',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'meta_query'     => array(
					array(
						'key'     => '_issslpg_template_page_id',
						'value'   => (string) $result->template_page_id,
						'compare' => '='
					),
					array(
						'key'     => '_issslpg_county_id',
						'value'   => (string) $result->county_id,
						'compare' => '='
					),
				),
			) );
			while ( $landing_pages->have_posts() ) :
				$landing_pages->the_post();
//				$title   = get_the_title();
//				$page_id = get_the_ID();
//				error_log( "Delete Page: {$title} ($page_id)" );
				wp_trash_post( get_the_ID() );
			endwhile;
			wp_reset_postdata();
		}

	}

	static public function untrash_landing_pages_by_county( $county_id ) {
		$county = new ISSSLPG_County_Data( $county_id );
		$active_cities = $county->get_active_cities();

		foreach( $active_cities as $active_city_id => $active_city_name ) {
			self::untrash_correlating_landing_pages_by_county_and_city( $county_id, $active_city_id );
//			error_log( "$active_city_id => $active_city_name" );
		}
	}

	static public function delete_correlating_landing_pages_by_template_page( $template_page_id ) {
		$landing_pages = new WP_Query( array(
			'post_type'      => 'issslpg-landing-page',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_query'     => array(
				'relation' => 'AND',
				array(
						'key'     => '_issslpg_template_page_id',
						'value'   => (string) $template_page_id,
						'compare' => '='
				),
				array(
						'key'     => '_issslpg_city_id',
						'compare' => 'EXISTS'
				),
			),
		) );
		while ( $landing_pages->have_posts() ) :
			$landing_pages->the_post();
			ISSSLPG_Logger::log( 'Trash landing page:' . get_the_title(), __METHOD__ );
			wp_trash_post( get_the_ID() );
		endwhile;
		wp_reset_postdata();
	}

	static public function delete_correlating_landing_pages_by_city( $city_id ) {
		$landing_pages = new WP_Query( array(
			'post_type'      => 'issslpg-landing-page',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => '_issslpg_city_id',
					'value'   => (string) $city_id,
					'compare' => '='
				),
			),
		) );
		while ( $landing_pages->have_posts() ) :
			$landing_pages->the_post();
			ISSSLPG_Logger::log( 'Trash landing page:' . get_the_title(), __METHOD__ );
			wp_trash_post( get_the_ID() );
		endwhile;
		wp_reset_postdata();
	}

	static public function maybe_activate_or_deactivate_city( $page_id ) {

		if ( ! self::is_landing_page( $page_id ) ) {
			return false;
		}

		$city_id = get_post_meta( $page_id, '_issslpg_city_id', true );

		if ( ! $city_id ) {
			return false;
		}

		$landing_pages = new WP_Query( array(
			'post_type'      => 'issslpg-landing-page',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_key'       => '_issslpg_city_id',
			'meta_value'     => (string) $city_id,
		) );

		$city_data = new ISSSLPG_City_Data( $city_id );
		$county_id = get_post_meta( $page_id, '_issslpg_county_id', true );

		// If there are other landing pages that are connected to the city,
		// activate city.
		if ( $landing_pages->post_count >= 1 ) {
			wp_reset_postdata();
			if ( ! $city_data->status ) {
				$city_data->update(
					array( 'active' => true ),
					$county_id
				);
			}
			return;
		}
		wp_reset_postdata();

		// If there are no landing pages that are connected to the city,
		// deactivate the city.
		if ( $city_data->status ) {
			$city_data->update( array( 'active' => false ) );
		}

	}

	static public function update_all_landing_pages() {
		global $wpdb;
		$city_data = CityData::where('active', '1')->get();
		foreach( $city_data as $city_data_entry ) {
			$wpdb->delete(
				"{$wpdb->prefix}issslpg_scheduled_landing_page_updates",
				array( 'city_id'          => $city_data_entry->city_id,
				       'county_id'        => NULL,
				       'template_page_id' => NULL,
				       'active'           => 1,
				       'method'           => 'update',
				),
				array( '%d', '%d', '%d', '%d', '%s' )
			);
			$wpdb->replace(
				"{$wpdb->prefix}issslpg_scheduled_landing_page_updates",
				array(
					'city_id'         => $city_data_entry->city_id,
					'county_id'        => NULL,
					'template_page_id' => NULL,
					'active'           => 1,
					'method'           => 'update',
				),
				array( '%d', '%d', '%d', '%d', '%s' )
			);
		}
		ISSSLPG_Logger::log( 'All landing pages are scheduled to update', __METHOD__ );
	}

	static public function update_all_custom_location_landing_pages() {
		$landing_pages = new WP_Query( array(
				'post_type'   => 'issslpg-landing-page',
				'post_status' => 'publish',
				'meta_query'  => array(
					array(
						'key'     => '_issslpg_location_hash',
						'compare' => 'EXISTS'
					),
				),
		) );

		while ( $landing_pages->have_posts() ) :
			$landing_pages->the_post();
		endwhile;
		wp_reset_postdata();
		ISSSLPG_Logger::log( 'All custom location landing pages are scheduled to update', __METHOD__ );
	}

}