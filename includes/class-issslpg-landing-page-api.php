<?php

class ISSSLPG_Landing_Page_Api {

	static public function get_phone_number() {
		$company_phone = esc_attr( ISSSLPG_Options::get_setting( 'company_phone', '000-000-0000', 'iss_company_info_settings' ) );
		$landing_page_default_phone = esc_attr( ISSSLPG_Options::get_setting( 'landing_page_default_phone', $company_phone ) );

		$fallback_phone = $company_phone;

		if ( ISSSLPG_Landing_Page::is_landing_page() ) {
			$fallback_phone = $landing_page_default_phone;
			$location       = new ISSSLPG_Public_Location();
			$location_phone = $location->inherited_phone;
			if ( ! empty( $location_phone ) ) {
				return $location_phone;
			}
		}

		return $fallback_phone;
	}

	static public function get_template_page_id() {
		return get_post_meta( get_the_ID(), '_issslpg_template_page_id', true );
	}

	static public function get_landing_page_id() {
		return ISSSLPG_Landing_Page::get_landing_page_id( false );
	}

	static public function get_heading() {
		return get_the_title();
	}

	static public function get_page_title() {
		return get_post_meta( get_the_ID(), '_issslpg_page_title', true );
	}

	// Deprecated
	static public function get_location_page_title() {
		return  get_post_meta( get_the_ID(), '_issslpg_page_title', true );
	}

	static public function get_template_page_title() {
		return get_the_title( self::get_template_page_id() );
	}

	static public function get_extended_page_title() {
		return get_the_title();
	}

	static public function get_map( $height = '', $width = '100%', $class = '' ) {
		$height      = empty( $height ) ? '300' : $height;
		$width       = empty( $width )  ? '100%' : $width;
		$location    = new ISSSLPG_Public_Location();
		$county_data = new ISSSLPG_County_Data(self::get_county_id());
		if ( $location->is_city_page() ) {
			$location = "{$location->name}, {$county_data->state}";
		} else {
			$location = "{$location->zip_code}, {$county_data->state}";
		}
		$location  = str_replace( ' ', '%20', $location );

		$output = '<iframe ';
		$output.=        " class='{$class}' ";
		$output.=        " style='height: {$height}px;' ";
		$output.=        " id='gmap_canvas' ";
		$output.=        " src='https://maps.google.com/maps?q={$location}&amp;t=&amp;z=13&amp;ie=UTF8&amp;iwloc=&amp;output=embed' ";
		$output.=        " width='{$width}' ";
		$output.=        " frameborder='0' ";
		$output.=        " marginwidth='0' ";
		$output.=        " marginheight='0' ";
		$output.=        " scrolling='no' ";
		$output.= '>';
		$output.= '</iframe>';

		return $output;
	}

	static public function get_directions_map( $height = '', $width = '100%', $class = '' ) {
		$api_key     = 'AIzaSyC3B9zmn-aqUMEXpTK3fxo-v_WhHVOCsZE';
		$height      = empty( $height ) ? '300' : $height;
		$width       = empty( $width )  ? '100%' : $width;
		$location    = new ISSSLPG_Public_Location();
		$county_data = new ISSSLPG_County_Data( self::get_county_id() );
		if ( $location->is_city_page() ) {
			$origin = "{$location->name},+{$county_data->state}";
		} else {
			$origin = "{$location->zip_code},+{$county_data->state}";
		}

		$office_google_pid = $county_data->office_google_pid;
		if ( is_null( $office_google_pid ) ) {
			$office_google_pid = $county_data->state_data_object->office_google_pid;
		}
		if ( ! is_null( $office_google_pid ) ) {
			$destination = "place_id:{$office_google_pid}";
		} else {
			$county_settings = $county_data->get_settings();
			if ( ! $county_settings ) {
				return false;
			}
			if (    ! trim( $county_settings['office_street'] )
			        || ! trim( $county_settings['office_city'] )
			        || ! trim( $county_settings['office_zip_code'] )
			) {
				return false;
			}
			$destination = "{$county_settings['office_street']},+{$county_settings['office_city']},+{$county_settings['office_zip_code']}";
		}

		$output = '<iframe ';
		$output.=        " src='";
		$output.=               "https://www.google.com/maps/embed/v1/directions";
		$output.=               "?key={$api_key}";
		$output.=               "&origin={$origin}";
		$output.=               "&destination={$destination}";
		$output.=        "' ";
		$output.=        " class='{$class}' ";
		$output.=        " width='{$width}' ";
		$output.=        " height='{$height}' ";
		$output.=        " frameborder='0' ";
		$output.=        " style='border:0;'' ";
		$output.=        " allowfullscreen='' ";
		$output.=        " aria-hidden='false' ";
		$output.=        " tabindex='0' ";
		$output.=        " scrolling='no' ";
		$output.= '>';
		$output.= '</iframe>';

		return $output;
	}

	static public function get_county_id() {
		return get_post_meta( get_the_ID(), '_issslpg_county_id', true );
	}

	static public function get_city_id() {
		return get_post_meta( get_the_ID(), '_issslpg_city_id', true );
	}

	static public function get_custom_location_hash() {
		return get_post_meta( get_the_ID(), '_issslpg_location_hash', true );
	}

	static public function get_cities_in_county( $limit = 5 ) {
		$landing_page_id = ISSSLPG_Landing_Page::get_landing_page_id( false );

		if ( ! $landing_page_id ) {
			return false;
		}

		$output = array();

		$county_id = self::get_county_id();
		$template_page_id = self::get_template_page_id();

		$county_data = new ISSSLPG_County_Data( $county_id );
		$cities = $county_data->get_active_cities( $limit );

		if ( empty( $cities ) ) {
			return;
		}

		$city_ids = [];
		foreach ( $cities as $city_id => $city_name ) {
			$city_ids[] = $city_id;
		}

		$landing_pages = new WP_Query( [
			'post_type'      => array( 'issslpg-landing-page' ),
			'post_status'    => array( 'publish' ),
			'posts_per_page' => $limit,
			'post__not_in'   => array( $landing_page_id ),
//				'orderby'        => 'rand',
			'meta_query' => array(
				array(
					'key'   => '_issslpg_city_id',
					'value' => $city_ids,
				),
				array(
					'key'   => '_issslpg_county_id',
					'value' => $county_id,
				),
				array(
					'key'   => '_issslpg_template_page_id',
					'value' => $template_page_id,
				),
			),
		] );

		if ( $landing_pages->have_posts() ) {
			for ( $i = 0; $landing_pages->have_posts(); $i++ ) {
				$landing_pages->the_post();
				$landing_page_city_id = get_post_meta( get_the_ID(), '_issslpg_city_id', true );
				$city_data = new ISSSLPG_City_Data( $landing_page_city_id );
				$landing_page_url = get_the_permalink( get_the_ID() );
				$output[] = "<a href='$landing_page_url'>{$city_data->name}</a>";
			}
			wp_reset_postdata();
		}

		if ( ! empty( $output ) ) {
			shuffle( $output );
			return $output;
		}
	}

	static public function get_related_landing_pages( $limit = 10, $thumbnail_size = 'post-thumbnail', $excerpt_length = 55, $handle = 'related_landing_pages' ) {

		// TEMPORARY FIX
//		if ( $limit > 3) {
//			$limit = 3;
//		}

		$landing_page_id = ISSSLPG_Landing_Page::get_landing_page_id();
		$cache_manager = false;
		$cached_landing_page_ids = null;
		if ( ISSSLPG_ISSSCR_Functions::has_cache_manager() && ! empty( $handle ) ) {
			$cache_manager = new ISSSCR_Cache_Manager();
			$cache_manager->load_record( $landing_page_id );
			$cached_landing_page_ids = $cache_manager->get_current_record_entry( $handle );
		}

		// Get template page, county, and city IDs
		$template_page_id     = self::get_template_page_id();
		$county_id            = self::get_county_id();
		$city_id              = self::get_city_id();
		$custom_location_hash = self::get_custom_location_hash();

		// Get category IDs
		$categories = wp_get_post_terms( $template_page_id, 'issslpg-template-category' );

		// Return if no categories are assigned
		if ( empty( $categories ) ) {
			return;
		}

		// Get category id
		$category_id = $categories[0]->term_id;

		// Get the IDs of all template pages with the same category
		$template_pages_ids = [];
		$template_pages = new WP_Query( [
			'post_type'      => array( 'issslpg-template' ),
			'post_status'    => array( 'publish' ),
			'post__not_in'   => array( $template_page_id ),
			'posts_per_page' => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'issslpg-template-category',
					'field'    => 'term_id',
					'terms'    => $category_id,
				),
			),
		] );
		if ( $template_pages->have_posts() ) {
			while ( $template_pages->have_posts() ) {
				$template_pages->the_post();
				$template_pages_ids[] = get_the_ID();
			}
		}
		wp_reset_postdata();

		// Return if no other template pages with the same categories are found
		if ( empty( $template_pages_ids ) ) {
			return;
		}

		if ( ! is_null( $cached_landing_page_ids ) && $cached_landing_page_ids ) {
			if ( ! is_array( $cached_landing_page_ids ) ) {
				$cached_landing_page_ids = array( $cached_landing_page_ids );
			}
			$landing_pages = new WP_Query( [
				'post_type' => array( 'issslpg-landing-page' ),
				'post__in'  => $cached_landing_page_ids
			] );
		} else {
			$location_query = array(
				'key'   => '_issslpg_city_id',
				'value' => $city_id,
			);
			if ( $custom_location_hash ) {
				$location_query = array(
					'key'   => '_issslpg_location_hash',
					'value' => $custom_location_hash,
				);
			}
			$landing_pages = new WP_Query( [
				'post_type'      => array( 'issslpg-landing-page' ),
				'post_status'    => array( 'publish' ),
				'posts_per_page' => $limit,
				'post__not_in'   => array( get_the_ID() ),
				'orderby'        => 'rand',
				'meta_query' => array(
					$location_query,
					array(
						'key'   => '_issslpg_template_page_id',
						'value' => $template_pages_ids,
					),
				),
			] );
		}

		// Get landing page data that belong to the same city and to the
		// template pages that are assigned to the same categories as the
		// template page that the current landing page is based on
		$landing_pages_data = [];
		if ( $landing_pages->have_posts() ) {
			// $i = 0;
			// while ( $landing_pages->have_posts() ) {
			for ( $i = 0; $landing_pages->have_posts(); $i++ ) {
				$landing_pages->the_post();

				$template_page_id = get_post_meta( get_the_ID(), '_issslpg_template_page_id', true );
				$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $template_page_id ), $thumbnail_size );

				//$landing_page_template_id = get_post_meta( get_the_ID(), '_issslpg_template_page_id', true );

				$landing_pages_data[$i]['id']               = get_the_ID();
				$landing_pages_data[$i]['title']            = get_the_title();
				$landing_pages_data[$i]['permalink']        = get_the_permalink();
//				$excerpt                                    = get_the_excerpt( $landing_page_template_id );
//				$landing_pages_data[$i]['excerpt']          = wp_trim_words( $excerpt, $excerpt_length );
				$landing_pages_data[$i]['excerpt']          = '';
				$landing_pages_data[$i]['page_title']       = get_post_meta( get_the_ID(), '_issslpg_page_title', true );
				$landing_pages_data[$i]['template_page_id'] = $template_page_id;
				$landing_pages_data[$i]['county_id']        = get_post_meta( get_the_ID(), '_issslpg_county_id', true );
				$landing_pages_data[$i]['city_id']          = get_post_meta( get_the_ID(), '_issslpg_city_id', true );
				$landing_pages_data[$i]['location_hash']    = get_post_meta( get_the_ID(), '_issslpg_location_hash', true );
				$landing_pages_data[$i]['thumbnail']        = $thumbnail[0];
				// $i++;
				if ( $cache_manager && is_null( $cached_landing_page_ids ) ) {
					$cache_manager->add_new_record_entry_value( $handle, $landing_pages_data[$i]['id'] );
				}
			}
		}
		wp_reset_postdata();

//		if ( $cache_manager ) {
//			$cache_manager->save_new_record();
//		}

		return $landing_pages_data;
	}

	static public function get_county_settings( $setting = false ) {
		$county_data = new ISSSLPG_County_Data( self::get_county_id() );
		if ( ! $county_data ) {
			return false;
		}
		$county_settings = $county_data->get_settings();
		if ( ! $county_settings ) {
			return false;
		}

		if ( $setting ) {
			return isset( $county_settings[$setting] ) ? $county_settings[$setting] : false;
		}

		return $county_settings;
	}



}