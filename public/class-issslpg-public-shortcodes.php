<?php

class ISSSLPG_Public_Shortcodes {

	private $location;

	private $city_data;

	private $helper;

	private $cache_manager;

	private $randomization;

	public function __construct() {
		$this->location = new ISSSLPG_Public_Location();
		$this->city_data = new ISSSLPG_City_Data();
		$this->helper = new ISSSLPG_Public_Shortcode_Helpers();
		$this->randomization = new ISSSLPG_Public_Randomization();
		$this->cache_manager = false;
		if ( ISSSLPG_ISSSCR_Functions::has_cache_manager() ) {
			$this->cache_manager = new ISSSCR_Cache_Manager();
		}
	}

	public function large_market_content( $atts, $content = null, $sc_name = '' ) {
		$landing_page_id = ISSSLPG_Landing_Page::get_landing_page_id();
		$cached_content_id = null;
		if ( $this->cache_manager ) {
			$this->cache_manager->load_record( $landing_page_id );
			$cached_content_id = $this->cache_manager->get_current_record_entry_value( $sc_name );
		}

		if ( ! is_null( $cached_content_id ) ) {
			$random_content_id = $cached_content_id;
		} else {
			$pinned_content_block_number = get_post_meta( $landing_page_id, '_issslpg_pinned_large_market_content_block', true );
			if ( ! empty ( $pinned_content_block_number ) ) {
				$random_content_id = $pinned_content_block_number;
			} else {
				$random_content_id = $this->randomization->get_random_content_id( '_issslpg_large_market_content', 'content', $landing_page_id );
				if ( $this->cache_manager ) {
					$this->cache_manager->add_new_record_entry_value( $sc_name, $random_content_id );
				}
			}
		}

		return ISSSLPG_Meta_Data::get_processed_content( '_issslpg_large_market_content', 'content', $random_content_id, $landing_page_id );
	}

	public function alternative_large_market_content( $atts, $content = null, $sc_name = '' ) {
		$landing_page_id = ISSSLPG_Landing_Page::get_landing_page_id();
		$cached_content_id = null;
		if ( $this->cache_manager ) {
			$this->cache_manager->load_record( $landing_page_id );
			$cached_content_id = $this->cache_manager->get_current_record_entry_value( $sc_name );
		}

		if ( ! is_null( $cached_content_id ) ) {
			$random_content_id = $cached_content_id;
		} else {
			$pinned_content_block_number = get_post_meta( $landing_page_id, '_issslpg_pinned_alternative_large_market_content_block', true );
			if ( ! empty ( $pinned_content_block_number ) ) {
				$random_content_id = $pinned_content_block_number;
			} else {
				$random_content_id = $this->randomization->get_random_content_id( '_issslpg_alternative_large_market_content', 'content', $landing_page_id );
				if ( $this->cache_manager ) {
					$this->cache_manager->add_new_record_entry_value( $sc_name, $random_content_id );
				}
			}
		}

		return ISSSLPG_Meta_Data::get_processed_content( '_issslpg_alternative_large_market_content', 'content', $random_content_id, $landing_page_id );
	}

	public function local_static_content( $atts, $content = null, $sc_name = '' ) {
		extract( shortcode_atts( array(
			'block' => '1',
		), $atts ) );

		$landing_page_id = ISSSLPG_Landing_Page::get_landing_page_id();

		return ISSSLPG_Meta_Data::get_processed_content( '_issslpg_local_static_content', 'content', $block, $landing_page_id );
	}

	public function local_image( $atts, $content = null, $sc_name = '' ) {
		$landing_page_id = ISSSLPG_Landing_Page::get_landing_page_id();
		$cached_image_id = null;
		if ( $this->cache_manager ) {
			$this->cache_manager->load_record( $landing_page_id );
			$cached_image_id = $this->cache_manager->get_current_record_entry_value( $sc_name );
		}

		extract( shortcode_atts( array(
			'size' => 'large',
			'class' => '',
			'alignment' => '',
		), $atts ) );

		$alignment = ( in_array( $alignment, ['left', 'center', 'right'] ) ) ? "align{$alignment}" : $alignment;
		$class = "{$class} {$alignment}";

		if ( ! is_null( $cached_image_id ) ) {
			$random_image_id = $cached_image_id;
		} else {
			$no_duplicates = get_post_meta( $landing_page_id, "_issslpg_no_duplicate_local_images", true );
			if ( $no_duplicates ) {
				$random_image_id = $this->randomization->get_random_image_id_without_duplicates( '_issslpg_local_images', 'image_id', $landing_page_id );
			} else {
				$random_image_id = $this->randomization->get_random_image_id( '_issslpg_local_images', 'image_id', $landing_page_id );
			}
			if ( $this->cache_manager ) {
				$this->cache_manager->add_new_record_entry_value( $sc_name, $random_image_id );
			}
		}

        $image = wp_get_attachment_image( $random_image_id, $size, false, array( 'class' => $class, 'style' => 'max-width: 100%; height: auto;' ) );

		return "<figure class='wp-block-image'>{$image}</figure>";
	}

	public function page_title( $atts, $content = null, $sc_name = '' ) {
		$template_page_id = get_post_meta( get_the_ID(), '_issslpg_template_page_id', true );
		$page_title = get_the_title( $template_page_id );
//		$page_title = get_the_title( get_the_ID() );
		$page_title = $this->helper->add_prefix( $page_title, $atts );
		if ( isset( $atts['lowercase'] ) && 'on' == $atts['lowercase'] ) {
			$page_title = strtolower( $page_title );
		}
		return $page_title;
	}

	public function site_name( $atts, $content = null, $sc_name = '' ) {
		$site_name = get_bloginfo( 'name' );
		$site_name = $this->helper->add_prefix( $site_name, $atts );
		return $site_name;
	}

	public function country( $atts, $content = null, $sc_name = '' ) {
		return $this->helper->add_prefix( $this->location->country, $atts );
	}

	public function state( $atts, $content = null, $sc_name = '' ) {
		return $this->helper->add_prefix( $this->location->state, $atts );
	}

	public function state_abbr( $atts, $content = null, $sc_name = '' ) {
		$state = $this->location->state_abbr;
		$state = $this->helper->add_prefix( $state, $atts );
		return $state;
	}

	public function county( $atts, $content = null, $sc_name = '' ) {
		return $this->helper->add_prefix( $this->location->county, $atts );
	}

	public function cities_in_county( $atts, $content = null, $sc_name = '' ) {
		$cached_cities = null;
		if ( $this->cache_manager ) {
			$landing_page_id = ISSSLPG_Landing_Page::get_landing_page_id();
			$this->cache_manager->load_record( $landing_page_id );
			$cached_cities = $this->cache_manager->get_current_record_entry_value( $sc_name );
		}

		if ( ! is_null( $cached_cities ) ) {
			$cities = $cached_cities;
		} else {
			$cities = ISSSLPG_Landing_Page_Api::get_cities_in_county();
			if ( $this->cache_manager ) {
				$this->cache_manager->add_new_record_entry_value( $sc_name, $cities );
			}
		}

		if ( ! empty( $cities ) ) {
//			$output = $this->helper->limit( $output, $atts, 10 );
			$output = $this->helper->join( $cities, $atts );
			$output = $this->helper->add_prefix( $output, $atts );
			return $output;
		}
	}

	public function counties( $atts, $content = null, $sc_name = '' ) {
		$counties = $this->location->counties;
		$counties = $this->helper->join( $counties, $atts );
		$counties = $this->helper->add_prefix( $counties, $atts );
		return $counties;
	}

	public function city( $atts, $content = null, $sc_name = '' ) {
		return $this->helper->add_prefix( $this->location->name, $atts );
	}

	public function city_state( $atts, $content = null, $sc_name = '' ) {
		$city  = $this->location->name;
		$state = $this->location->state;
		$city_state = $this->helper->join( [$city, $state], $atts );
		$city_state = $this->helper->add_prefix( $city_state, $atts );
		return $city_state;
	}

	public function city_state_abbr( $atts, $content = null, $sc_name = '' ) {
		$city  = $this->location->name;
		$state = $this->location->state_abbr;
		$city_state = $this->helper->join( [$city, $state], $atts );
		$city_state = $this->helper->add_prefix( $city_state, $atts );
		return $city_state;
	}

	public function city_state_zip_code( $atts, $content = null, $sc_name = '' ) {
		$city     = $this->location->name;
		$state    = $this->location->state;
		$zip_code = $this->location->zip_code;
		$city_state_zip_code = $this->helper->join( [$city, $state, $zip_code], $atts );
		$city_state_zip_code = $this->helper->add_prefix( $city_state_zip_code, $atts );
		return $city_state_zip_code;
	}

	public function city_state_abbr_zip_code( $atts, $content = null, $sc_name = '' ) {
		$city     = $this->location->name;
		$state    = $this->location->state_abbr;
		$zip_code = $this->location->zip_code;
		$city_state_zip_code = $this->helper->join( [$city, $state, $zip_code], $atts );
		$city_state_zip_code = $this->helper->add_prefix( $city_state_zip_code, $atts );
		return $city_state_zip_code;
	}

	public function site_name_city_state_zip_code( $atts, $content = null, $sc_name = '' ) {
		$site_name = get_bloginfo( 'name' );
		$city      = $this->location->name;
		$state     = $this->location->state;
		$zip_code  = $this->location->zip_code;
		$city_state_zip_code = $this->helper->join( [$site_name . ' in ' . $city, $state, $zip_code], $atts );
		$city_state_zip_code = $this->helper->add_prefix( $city_state_zip_code, $atts );
		return $city_state_zip_code;
	}

	public function site_name_city_state_abbr_zip_code( $atts, $content = null, $sc_name = '' ) {
		$site_name = get_bloginfo( 'name' );
		$city      = $this->location->name;
		$state     = $this->location->state_abbr;
		$zip_code  = $this->location->zip_code;
		$city_state_zip_code = $this->helper->join( [$site_name . ' in ' . $city, $state, $zip_code], $atts );
		$city_state_zip_code = $this->helper->add_prefix( $city_state_zip_code, $atts );
		return $city_state_zip_code;
	}

	public function page_title_city_state_zip_code( $atts, $content = null, $sc_name = '' ) {
//		global $post;
//		$post = get_post( $post );
//		$page_title = isset( $post->post_title ) ? $post->post_title : '';
//		$page_title = strip_shortcodes( $page_title );
		$template_page_id = get_post_meta( get_the_ID(), '_issslpg_template_page_id', true );
		$page_title = get_the_title( $template_page_id );

		$city     = $this->location->name;
		$state    = $this->location->state;
		$zip_code = $this->location->zip_code;

		$city_state_zip_code = $this->helper->join( [$page_title . ' in ' . $city, $state, $zip_code], $atts );
		$city_state_zip_code = $this->helper->add_prefix( $city_state_zip_code, $atts );

		return $city_state_zip_code;
	}

	public function page_title_city_state_abbr_zip_code( $atts, $content = null, $sc_name = '' ) {
//		global $post;
//		$post = get_post( $post );
//		$page_title = isset( $post->post_title ) ? $post->post_title : '';
//		$page_title = strip_shortcodes( $page_title );
		$template_page_id = get_post_meta( get_the_ID(), '_issslpg_template_page_id', true );
		$page_title = get_the_title( $template_page_id );

		$city     = $this->location->name;
		$state    = $this->location->state_abbr;
		$zip_code = $this->location->zip_code;

		$city_state_zip_code = $this->helper->join( [$page_title . ' in ' . $city, $state, $zip_code], $atts );
		$city_state_zip_code = $this->helper->add_prefix( $city_state_zip_code, $atts );

		return $city_state_zip_code;
	}

	public function city_state_zip_code_phone_number( $atts, $content = null, $sc_name = '' ) {
		$city         = $this->location->name;
		$state        = $this->location->state;
		$zip_code     = $this->location->zip_code;
		$phone_number = $this->location->inherited_phone;
		$city_state_zip_code_phone_number = $this->helper->join( [$city, $state, $zip_code, $phone_number], $atts );
		$city_state_zip_code_phone_number = $this->helper->add_prefix( $city_state_zip_code_phone_number, $atts );
		return $city_state_zip_code_phone_number;
	}

	public function city_state_abbr_zip_code_phone_number( $atts, $content = null, $sc_name = '' ) {
		$city         = $this->location->name;
		$state        = $this->location->state_abbr;
		$zip_code     = $this->location->zip_code;
		$phone_number = $this->location->inherited_phone;
		$city_state_zip_code_phone_number = $this->helper->join( [$city, $state, $zip_code, $phone_number], $atts );
		$city_state_zip_code_phone_number = $this->helper->add_prefix( $city_state_zip_code_phone_number, $atts );
		return $city_state_zip_code_phone_number;
	}

	public function map( $atts, $content = null, $sc_name = '' ) {
		$height = isset( $atts['height'] ) ? $atts['height'] : '';
		$width  = isset( $atts['width'] )  ? $atts['width']  : '';
		$class  = isset( $atts['class'] )  ? $atts['class']  : '';
		return ISSSLPG_Landing_Page_Api::get_map( $height, $width, $class );
	}

	public function directions_map( $atts, $content = null, $sc_name = '' ) {
		$height = isset( $atts['height'] ) ? $atts['height'] : '';
		$width  = isset( $atts['width'] )  ? $atts['width']  : '';
		$class  = isset( $atts['class'] )  ? $atts['class']  : '';
		return ISSSLPG_Landing_Page_Api::get_directions_map( $height, $width, $class );
	}

	public function city_county( $atts, $content = null, $sc_name = '' ) {
		$city   = $this->location->name;
		$county = $this->location->county;
		$city_county = $this->helper->join( [$city, $county], $atts );
		$city_county = $this->helper->add_prefix( $city_county, $atts );
		return $city_county;
	}

	public function zip_code( $atts, $content = null, $sc_name = '' ) {
		$zip_code = $this->location->zip_code;
		$zip_code = $this->helper->add_prefix( $zip_code, $atts );
		return $zip_code;
	}

	public function zip_codes( $atts, $content = null, $sc_name = '' ) {
		$cached_zip_codes = null;
		if ( $this->cache_manager ) {
			$landing_page_id = ISSSLPG_Landing_Page::get_landing_page_id();
			$this->cache_manager->load_record( $landing_page_id );
			$cached_zip_codes = $this->cache_manager->get_current_record_entry_value( $sc_name );
		}
		if ( ! is_null( $cached_zip_codes ) ) {
			$zip_codes = $cached_zip_codes;
		} else {
			$zip_codes = $this->location->zip_codes;
			if ( $zip_codes ) {
				shuffle( $zip_codes );
			}
			if ( $this->cache_manager ) {
				$this->cache_manager->add_new_record_entry_value( $sc_name, $zip_codes );
			}
		}

		$zip_codes = $this->helper->limit( $zip_codes, $atts, 10 );
		$zip_codes = $this->helper->join( $zip_codes, $atts );
		$zip_codes = $this->helper->add_prefix( $zip_codes, $atts );

		return $zip_codes;
	}

	public function random_location_format( $atts, $content = null, $sc_name = '' ) {
		$cached_value = null;
		if ( $this->cache_manager ) {
			$landing_page_id = ISSSLPG_Landing_Page::get_landing_page_id();
			$this->cache_manager->load_record( $landing_page_id );
			$cached_value = $this->cache_manager->get_current_record_entry_value( $sc_name );
		}

		if ( ! is_null( $cached_value ) ) {
			$output = $cached_value;
		} else {
			$shortcodes = array();
			$shortcodes[] = $this->city( array() );
			$shortcodes[] = $this->city_state( array() );
			$shortcodes[] = $this->city_state_abbr( array() );
			$shortcodes[] = $this->city_state_zip_code( array() );
			$shortcodes[] = $this->city_state_abbr_zip_code( array() );
			$output = $shortcodes[ array_rand( $shortcodes ) ];
			if ( $this->cache_manager ) {
				$this->cache_manager->add_new_record_entry_value( $sc_name, $output );
			}
		}

		return $output;
	}

	public function phone( $atts, $content = null, $sc_name = '' ) {
		$phone = ISSSLPG_Landing_Page_Api::get_phone_number();
		$phone = $this->helper->add_prefix_and_suffix( $phone, $atts );
		return $phone;
	}

	public function phone_link( $atts, $content = null, $sc_name = '' ) {

		//
		// Attributes
		//
		$atts = shortcode_atts( array(
			'font_size'        => 'default',
			'font_weight'      => 'bold',
			'font_style'       => 'default',
			'text_decoration'  => 'default',
			'prefix'           => '',
			'title'            => '',
			'suffix'           => '',
			'target'           => '',
			'link_color'       => '',
			'link_hover_color' => '',
		), $atts, $sc_name );
		$output = '';
		$random_number = rand( 1, 999999 );

		//
		// CSS
		//
		$inline_style = empty( $atts['link_color'] ) ? '' : "color: {$atts['link_color']};";
		$js_color = empty( $atts['link_hover_color'] ) ? '' : "onmouseout='this.style.color=\"{$atts['link_color']}\"'";
		$js_hover_color = empty( $atts['link_hover_color'] ) ? '' : "onmouseover='this.style.color=\"{$atts['link_hover_color']}\"'";

		//
		// Output
		//
		$phone_number = ISSSLPG_Landing_Page_Api::get_phone_number();
		$phone_title = empty( $atts['title'] ) ? $phone_number : $atts['title'];
		$phone_title = $this->helper->add_prefix_and_suffix( $phone_title, $atts );
		$output.= "<a href='tel:{$phone_number}' target='{$atts['target']}' style='{$inline_style}' {$js_color} {$js_hover_color} class='issslpg-phone-link  issslpg-phone-link--{$random_number}  issslpg-phone-link--font-size-{$atts['font_size']}  issslpg-phone-link--font-weight-{$atts['font_weight']}  issslpg-phone-link--font-style-{$atts['font_style']}  issslpg-phone-link--text-decoration-{$atts['text_decoration']}'>";
		$output.=     $phone_title;
		$output.= '</a>';

		//
		// Return
		//
		return $output;
	}

	public function cta_button( $atts, $content = null, $sc_name = '' ) {

		if ( ! ISSSLPG_Helpers::is_cta_button_usage_allowed() ) {
			return;
		}

		//
		// Attributes
		//
		$atts = shortcode_atts( array(
			'type'           => '',
			'prefix'         => '',
			'title'          => '',
			'href'           => '',
			'target'         => '',
			'suffix'         => '',
			'style'          => 'default',
			'size'           => 'medium',
			'font_style'     => 'default',
			'rounded'        => 'not-rounded',
			'shadow'         => 'no',
			'icon'           => '',
			'width'          => 'auto',
			'text_color'     => '#ffffff',
			'bg_color'       => '#000000',
			'hover_bg_color' => '#333333',
		), $atts, $sc_name );

		//
		// Title & Href
		//
		$default_title = 'Button Title';
		$default_href = '#';
		switch ( $atts['type'] ) {
			case 'phone':
				$default_title = ISSSLPG_Landing_Page_Api::get_phone_number();
				$default_href = "tel:{$this->location->inherited_phone}";
				break;
			case 'email':
				$email = esc_attr( ISSSLPG_Options::get_setting( 'default_email', get_bloginfo( 'admin_email' ) ) );
				$default_title = $email;
				$default_href = "mailto:{$email}";
				break;
		}
		$title = empty( $atts['title'] ) ? $default_title : $atts['title'];
		$href = empty( $atts['href'] ) ? $default_href : $atts['href'];

		//
		// CSS
		//
		$random_number = rand( 1, 999999 );
		$output = '<style>';
		switch( $atts['style'] ) {
			case 'outline':
				$output.= ".issslpg-cta-button--outline.issslpg-cta-button--{$random_number},";
				$output.= ".issslpg-cta-button--outline.issslpg-cta-button--{$random_number}:visited {";
				$output.=     "color: {$atts['bg_color']};";
				$output.=     "border: .2em solid {$atts['bg_color']};";
				$output.= '}';
				$output.= ".issslpg-cta-button--outline.issslpg-cta-button--{$random_number}:hover,";
				$output.= ".issslpg-cta-button--outline.issslpg-cta-button--{$random_number}:active {";
				$output.=     "color: {$atts['text_color']};";
				$output.=     "border-color: {$atts['hover_bg_color']};";
				$output.=     "background-color: {$atts['hover_bg_color']};";
				$output.= '}';
				$output.= ".issslpg-cta-button--outline.issslpg-cta-button--{$random_number} .issslpg-cta-button__icon svg {";
				$output.=     "fill: {$atts['bg_color']};";
				$output.= '}';
				$output.= ".issslpg-cta-button--outline.issslpg-cta-button--{$random_number}:hover .issslpg-cta-button__icon svg {";
				$output.=     "fill: {$atts['text_color']};";
				$output.= '}';
				break;
			default:
				$output.= ".issslpg-cta-button--styled.issslpg-cta-button--{$random_number},";
				$output.= ".issslpg-cta-button--styled.issslpg-cta-button--{$random_number}:visited {";
				$output.=     "color: {$atts['text_color']};";
				$output.=     "background-color: {$atts['bg_color']};";
				$output.= '}';
				$output.= ".issslpg-cta-button--styled.issslpg-cta-button--{$random_number}:hover,";
				$output.= ".issslpg-cta-button--styled.issslpg-cta-button--{$random_number}:active {";
				$output.=     "color: {$atts['text_color']};";
				$output.=     "background-color: {$atts['hover_bg_color']};";
				$output.= '}';
				$output.= ".issslpg-cta-button--styled.issslpg-cta-button--{$random_number} .issslpg-cta-button__icon svg {";
				$output.=     "fill: {$atts['text_color']};";
				$output.= '}';
				break;
		}
		$output.= '</style>';

		//
		// Open
		//
		$styled_class = ( $atts['style'] != 'none' ) ? 'issslpg-cta-button--styled' : '';
		$output.= "<a href='{$href}' target='{$atts['target']}' class='issslpg-cta-button  {$styled_class}  issslpg-cta-button--{$atts['style']}  issslpg-cta-button--{$atts['size']}  issslpg-cta-button--{$atts['width']}  issslpg-cta-button--{$atts['rounded']}  issslpg-cta-button--{$atts['shadow']}-shadow  issslpg-cta-button-font-style--{$atts['font_style']}  issslpg-cta-button--{$random_number}'>";
		$output.= '<span class="issslpg-cta-button__inner-wrapper">';

		//
		// Icon
		//
		if ( ! empty( $atts['icon'] ) ) {
			switch ( $atts['icon'] ) {
				case 'phone-1':
					$icon = '<svg viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg"><path d="M300 236.932c0 3.835-.71 8.842-2.13 15.021-1.421 6.18-2.913 11.044-4.475 14.595-2.983 7.103-11.648 14.631-25.994 22.586C254.048 296.378 240.838 300 227.77 300c-3.835 0-7.564-.249-11.186-.746s-7.706-1.385-12.252-2.663c-4.545-1.279-7.919-2.308-10.12-3.09-2.202-.78-6.144-2.237-11.826-4.367-5.681-2.131-9.162-3.41-10.44-3.836-13.92-4.971-26.35-10.866-37.287-17.684-18.182-11.222-36.967-26.527-56.356-45.917-19.39-19.389-34.695-38.174-45.917-56.356-6.818-10.938-12.713-23.367-17.684-37.287-.426-1.278-1.705-4.759-3.836-10.44-2.13-5.682-3.586-9.624-4.367-11.826-.782-2.201-1.811-5.575-3.09-10.12-1.278-4.546-2.166-8.63-2.663-12.252A82.193 82.193 0 010 72.23C0 59.162 3.622 45.952 10.866 32.6 18.821 18.252 26.35 9.587 33.452 6.604c3.55-1.562 8.416-3.054 14.595-4.474C54.226.71 59.233 0 63.068 0c1.989 0 3.48.213 4.475.64 2.556.851 6.32 6.25 11.292 16.192 1.563 2.7 3.693 6.534 6.392 11.506 2.7 4.972 5.185 9.482 7.458 13.53a457.555 457.555 0 006.605 11.399c.426.568 1.669 2.344 3.728 5.327 2.06 2.983 3.587 5.504 4.581 7.564.995 2.06 1.492 4.083 1.492 6.072 0 2.841-2.024 6.392-6.073 10.654a103.862 103.862 0 01-13.21 11.718c-4.758 3.551-9.162 7.316-13.21 11.293-4.048 3.977-6.072 7.244-6.072 9.801 0 1.278.355 2.876 1.065 4.794.71 1.918 1.314 3.374 1.811 4.368.497.994 1.491 2.699 2.983 5.114 1.491 2.414 2.308 3.764 2.45 4.048 10.796 19.46 23.154 36.15 37.074 50.07 13.92 13.921 30.61 26.28 50.071 37.075.284.142 1.634.959 4.048 2.45 2.415 1.492 4.12 2.486 5.114 2.983.994.497 2.45 1.1 4.368 1.811 1.918.71 3.516 1.065 4.794 1.065 2.557 0 5.824-2.024 9.801-6.072 3.977-4.048 7.742-8.452 11.293-13.21a103.862 103.862 0 0111.718-13.21c4.262-4.049 7.813-6.073 10.654-6.073 1.989 0 4.013.497 6.072 1.492 2.06.994 4.581 2.52 7.564 4.58 2.983 2.06 4.759 3.303 5.327 3.73 3.551 2.13 7.35 4.332 11.4 6.604 4.047 2.273 8.557 4.759 13.529 7.458 4.972 2.699 8.807 4.83 11.506 6.392 9.943 4.971 15.34 8.736 16.193 11.292.426.995.639 2.486.639 4.475z" /></svg>';
					break;
				case 'phone-2':
					$icon = '<svg viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg"><path d="M250 208.008c0-1.432-.13-2.474-.39-3.125-.391-1.042-2.898-2.962-7.52-5.762-4.623-2.8-10.384-6.022-17.285-9.668l-10.352-5.664c-.65-.39-1.888-1.237-3.71-2.539-1.824-1.302-3.451-2.279-4.884-2.93-1.432-.65-2.8-.976-4.101-.976-2.344 0-5.404 2.116-9.18 6.347a680.113 680.113 0 00-11.133 12.793c-3.646 4.297-6.51 6.446-8.593 6.446-.912 0-1.986-.228-3.223-.684-1.237-.456-2.246-.879-3.027-1.27-.782-.39-1.888-1.009-3.32-1.855-1.433-.846-2.345-1.4-2.735-1.66-12.89-7.162-23.991-15.397-33.3-24.707-9.31-9.31-17.546-20.41-24.708-33.3-.26-.392-.814-1.303-1.66-2.735-.846-1.433-1.465-2.54-1.856-3.32-.39-.782-.813-1.79-1.27-3.028-.455-1.237-.683-2.311-.683-3.223 0-1.692 1.335-3.873 4.004-6.543a76.211 76.211 0 018.79-7.52 66.574 66.574 0 008.788-7.714c2.67-2.8 4.004-5.176 4.004-7.129 0-1.302-.325-2.67-.976-4.101-.651-1.433-1.628-3.06-2.93-4.883-1.302-1.823-2.148-3.06-2.54-3.711-.39-.781-1.366-2.637-2.929-5.567a414.065 414.065 0 00-4.883-8.886 981.975 981.975 0 01-5.175-9.278c-1.758-3.19-3.386-5.826-4.883-7.91-1.498-2.083-2.572-3.255-3.223-3.515-.65-.26-1.693-.391-3.125-.391-6.25 0-12.825 1.432-19.726 4.297-5.99 2.734-11.198 8.887-15.625 18.457S50 90.82 50 98.242c0 2.084.163 4.297.488 6.64.326 2.345.651 4.33.977 5.958.325 1.627.911 3.776 1.758 6.445.846 2.67 1.497 4.59 1.953 5.762.456 1.172 1.27 3.32 2.441 6.445 1.172 3.125 1.888 5.078 2.149 5.86 7.812 21.354 21.907 42.22 42.285 62.597 20.377 20.378 41.243 34.473 62.597 42.285.782.26 2.735.977 5.86 2.149a678.597 678.597 0 016.445 2.441c1.172.456 3.093 1.107 5.762 1.953 2.67.847 4.818 1.433 6.445 1.758 1.628.326 3.613.651 5.957.977 2.344.325 4.557.488 6.64.488 7.423 0 15.919-2.214 25.49-6.64 9.57-4.428 15.722-9.636 18.456-15.626 2.865-6.9 4.297-13.476 4.297-19.726zM300 56.25v187.5c0 15.495-5.501 28.743-16.504 39.746C272.493 294.5 259.245 300 243.75 300H56.25c-15.495 0-28.743-5.501-39.746-16.504C5.5 272.493 0 259.245 0 243.75V56.25c0-15.495 5.501-28.743 16.504-39.746C27.507 5.5 40.755 0 56.25 0h187.5c15.495 0 28.743 5.501 39.746 16.504C294.5 27.507 300 40.755 300 56.25z" /></svg>';
					break;
				case 'email-1':
					$icon = '<svg viewBox="0 0 300 220" xmlns="http://www.w3.org/2000/svg"><path d="M255.23.5H44.77C20.024.563 0 20.605 0 45.305v129.23c0 24.7 20.025 44.742 44.77 44.806h210.46c24.745-.064 44.77-20.105 44.77-44.805V45.305C300 20.605 279.975.563 255.23.5zm0 23.036h1.847L150 101.397 42.923 23.536h212.308zm21.693 151c0 11.978-9.693 21.705-21.692 21.769H44.769c-11.999-.064-21.692-9.791-21.692-21.77V45.306a22.079 22.079 0 011.154-6.796l119.077 86.5a11.555 11.555 0 0013.615 0l118.962-86.5a22.079 22.079 0 011.153 6.796l-.115 129.23z" /></svg>';
					break;
				case 'facebook':
					$icon = '<svg viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg"><path d="M.65 274.4596c0 13.752 11.1397 24.8904 24.8904 24.8904H274.459c13.752 0 24.8904-11.1384 24.8904-24.8904V25.541c0-13.7514-11.1383-24.8904-24.8904-24.8904H25.5411C11.7897.6507.65 11.7904.65 25.541v248.9185zm155.5733-12.4466v-99.5664h-24.8918v-37.3384h24.8918c0-64.1582 2.735-68.4514 80.8986-68.4514v37.3377c-41.198 0-37.3383 2.3028-37.3383 31.1144h37.3383v37.3384h-37.3383v99.5657h-43.5603z"/></svg>';
					break;
				case 'instagram':
					$icon = '<svg viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg"><g><circle cx="148.9831" cy="152.0339" r="26.9492"/><path d="M182.6947 85.4237H115.679c-9.7477 0-18.277 3.0462-23.76 8.5293-5.4831 5.483-8.5293 14.0123-8.5293 23.76v67.0156c0 9.7477 3.0462 18.277 9.1385 24.3693 6.0923 5.483 14.0124 8.5292 23.76 8.5292h66.4064c9.7477 0 18.277-3.0461 23.76-8.5292 6.0924-5.4831 9.1385-14.0124 9.1385-23.76v-67.0156c0-9.7477-3.0461-17.6678-8.5292-23.76-6.0924-6.0924-14.0124-9.1386-24.3693-9.1386zm-33.5078 107.2249c-23.1508 0-41.4278-18.8862-41.4278-41.4278 0-23.1508 18.8862-41.4278 41.4278-41.4278s42.037 18.277 42.037 41.4278c0 23.1508-18.8862 41.4278-42.037 41.4278zm43.2555-74.9356c-5.483 0-9.7477-4.2646-9.7477-9.7477 0-5.483 4.2646-9.7477 9.7477-9.7477 5.483 0 9.7477 4.2646 9.7477 9.7477s-4.2646 9.7477-9.7477 9.7477z"/><path d="M149.998 0C67.0723 0 0 67.0732 0 150s67.0723 150 149.998 150c82.9257 0 149.998-67.0732 149.998-150 .6097-82.9268-67.0723-150-149.998-150zm79.877 185.3659c0 14.0243-4.878 26.2195-13.4145 34.756-8.5365 8.5366-20.7315 12.805-34.146 12.805h-66.4624c-13.4145 0-25.6094-4.2684-34.1459-12.805-9.1462-8.5365-13.4144-20.7317-13.4144-34.756v-67.0732c0-28.0488 18.9021-47.561 47.5603-47.561h67.0722c14.0242 0 25.6094 4.878 34.146 13.4146 8.5364 8.5366 12.8046 20.122 12.8046 34.1464v67.0732z"/></g></svg>';
					break;
				case 'linkedin':
					$icon = '<svg viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg"><g fill-rule="nonzero"><path d="M159.2412 135.8882v-.453c-.0941.153-.1941.306-.2941.453h.294z" fill="#000000"/><path d="M273.147 3.9235H25.3825c-11.8589 0-21.4765 9.3883-21.4765 20.9589V275.547c0 11.5647 9.6176 20.9529 21.4765 20.9529H273.147c11.8764 0 21.494-9.3941 21.494-20.953V24.8825c0-11.5765-9.6235-20.9589-21.494-20.9589zM92.0295 248.8471H48.1176V116.7353h43.9118V248.847zM70.0764 98.6882h-.294c-14.7295 0-24.2589-10.147-24.2589-22.8235 0-12.9588 9.8236-22.8235 24.8412-22.8235 15.0235 0 24.2647 9.8647 24.5588 22.8235 0 12.6765-9.5411 22.8235-24.847 22.8235zm180.3 150.1589h-43.9117v-70.6942c0-17.7529-6.353-29.8705-22.2412-29.8705-12.1294 0-19.353 8.1705-22.5353 16.0647-1.1588 2.8176-1.4411 6.7588-1.4411 10.7117v73.7824h-43.9118s.5765-119.7294 0-132.1118h43.9118v18.7c5.8353-9 16.2705-21.8176 39.5823-21.8176 28.8882 0 50.553 18.8882 50.553 59.4706v75.7647h-.006z" /></g></svg>';
					break;
				case 'pinterest':
					$icon = '<svg viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg"><path d="M299 150c0 82.2695-66.7305 149-149 149C67.6945 299 1 232.2695 1 150 1 67.6945 67.6945 1 150 1c82.2695 0 149 66.6945 149 149M160.4201 38.1414c-59.5059 0-94.1418 47.307-94.1418 82.887 0 21.5297 12.7797 40.6993 30.2792 47.8513 2.8685 1.1618 5.446.0366 6.2812-3.1589.581-2.1784 1.9246-7.7335 2.5415-10.057.8353-3.1224.5448-4.2116-1.7793-6.9708-5.0469-5.9542-8.2778-13.651-8.2778-24.5429 0-31.6589 23.6715-60.0142 61.6477-60.0142 33.656 0 52.1354 20.5492 52.1354 47.9966 0 36.1248-15.9746 66.6219-39.7188 66.6219-13.1067 0-22.9094-10.8558-19.7505-24.1438 3.7399-15.866 11.037-33.0025 11.037-44.4386 0-10.2383-5.4824-18.8065-16.8825-18.8065-13.3972 0-24.1438 13.8689-24.1438 32.4215 0 11.8358 3.9936 19.8231 3.9936 19.8231s-18.3708 58.0897-20.767 68.2554c-4.7924 20.2588 8.5682 54.3864 8.9313 56.8919.1818 1.4888 2.1057 1.8153 2.977.726 1.2345-1.634 21.8203-30.6062 27.2296-50.284 1.5614-5.5912-.4723-34.4548-.4723-34.4548 4.3567 8.3144 17.0639 20.2588 30.6063 20.2588 40.2637 0 72.213-41.353 72.213-90.4747 0-37.1408-36.1242-76.3873-83.939-76.3873" /></svg>';
					break;
				case 'tiktok':
					$icon = '<svg viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg"><path d="M265.2857 54.8571a10.7143 10.7143 0 00-10.0714-1.2857c-11.6747 4.986-25.0817 3.708-35.6043-3.3936s-16.7234-19.057-16.4671-31.7492a10.7143 10.7143 0 00-3-7.7143 10.7143 10.7143 0 00-7.7143-3.3214h-42.8572c-5.9173 0-10.7143 4.797-10.7143 10.7142v183.4286c0 14.7934-11.9923 26.7857-26.7857 26.7857-14.7933 0-26.7857-11.9923-26.7857-26.7857 0-14.7933 11.9924-26.7857 26.7857-26.7857 5.9174 0 10.7143-4.797 10.7143-10.7143v-42.8571c0-5.9174-4.797-10.7143-10.7143-10.7143-50.2729.059-91.0124 40.7985-91.0714 91.0714a90.1071 90.1071 0 0017.1429 53.5714 10.7143 10.7143 0 008.6785 4.5 10.7143 10.7143 0 006.3215-2.4642 10.7143 10.7143 0 002.4642-15 69 69 0 01-13.1785-40.6072c.0511-34.3226 25.0285-63.5234 58.9285-68.8928v21.4285c-24.5437 5.5953-40.652 29.131-36.983 54.0356 3.6692 24.9046 25.8806 42.7949 50.9951 41.0742 25.1146-1.7207 44.6784-22.4732 44.9165-47.6455V28.8214h22.1786c4.6669 28.5137 29.2855 49.46 58.1786 49.5a67.5 67.5 0 008.0357-.5357v20.7857a76.2857 76.2857 0 01-50.7857-8.8928 10.7143 10.7143 0 00-10.7143 0A10.7143 10.7143 0 00181.8214 99v102.5357c-.0535 23.2643-11.7024 44.9704-31.0594 57.875-19.3571 12.9048-43.8739 15.3091-65.3691 6.4107-5.3225-1.7812-11.1175.8468-13.2842 6.0243-2.1666 5.1775.0294 11.1496 5.0342 13.69a89.5785 89.5785 0 0035.1428 7.0718c47.5376-.0719 87.0631-36.6143 90.8572-84.0004a8.1429 8.1429 0 000-1.7142V115.607a97.8214 97.8214 0 0059.1428 1.7143c4.7278-1.3747 7.9101-5.7946 7.7143-10.7143V64.2857a10.7143 10.7143 0 00-4.7143-9.4286z" /></svg>';
					break;
				case 'twitter':
					$icon = '<svg viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg"><path d="M299.022 57.39c-10.866 4.83-22.56 8.088-34.83 9.546 12.528-7.5 22.134-19.38 26.664-33.552-11.718 6.954-24.69 12-38.508 14.724-11.058-11.784-26.82-19.152-44.262-19.152-33.486 0-60.636 27.15-60.636 60.642 0 4.746.534 9.372 1.572 13.818-50.4-2.532-95.088-26.67-124.998-63.36-5.22 8.952-8.208 19.368-8.208 30.492 0 21.036 10.704 39.6 26.976 50.472-9.936-.318-19.29-3.048-27.468-7.59-.006.252-.006.51-.006.768 0 29.376 20.904 53.88 48.648 59.46-5.088 1.38-10.446 2.124-15.978 2.124-3.912 0-7.71-.378-11.412-1.092 7.722 24.09 30.114 41.628 56.646 42.114-20.754 16.266-46.896 25.962-75.312 25.962-4.89 0-9.72-.288-14.466-.852 26.844 17.214 58.716 27.246 92.958 27.246 111.546 0 172.536-92.4 172.536-172.536 0-2.628-.054-5.25-.168-7.854 11.844-8.532 22.128-19.218 30.252-31.38z" /></svg>';
					break;
				case 'youtube':
					$icon = '<svg viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg"><path d="M290.937 95.712s-2.8123-19.8197-11.4375-28.5504C268.567 55.7082 256.3051 55.654 250.684 54.9828c-40.2467-2.9113-100.62-2.9113-100.62-2.9113h-.1278s-60.37 0-100.62 2.9113c-5.6212.6711-17.8767.7254-28.8156 12.1788-8.6252 8.7307-11.431 28.5505-11.431 28.5505s-2.8762 23.2743-2.8762 46.5486v21.8235c0 23.271 2.8762 46.5454 2.8762 46.5454s2.8058 19.8197 11.431 28.5504c10.9389 11.4566 25.31 11.0891 31.711 12.2907 23.0122 2.2082 97.7885 2.892 97.7885 2.892s60.4372-.0926 100.6839-3.0007c5.6212-.671 17.8831-.7254 28.8156-12.182 8.6252-8.7307 11.4374-28.5504 11.4374-28.5504s2.8698-23.2743 2.8698-46.5454v-21.8235c0-23.2743-2.8698-46.5486-2.8698-46.5486zm-170.6283 94.8102l-.0128-80.8098 77.7132 40.544-77.7004 40.2658z" /></svg>';
					break;
			}
//			$icon = plugin_dir_url( __FILE__ ) . "images/button-icons/{$atts['icon']}.svg";
			$output.= '<span class="issslpg-cta-button__primary">';
			$output.= '<span class="issslpg-cta-button__icon">';
//			$output.= "<img src='$icon'>";
			$output.= $icon;
			$output.= '</span>';
			$output.= '</span>';
		}

		$output.= '<span class="issslpg-cta-button__secondary">';

		//
		// Prefix
		//
		if ( ! empty( $atts['prefix'] ) ) {
			$output.= '<span class="issslpg-cta-button__prefix">';
			$output.= $atts['prefix'];
			$output.= '</span> ';
		}

		//
		// Phone Number
		//
		$output.= '<span class="issslpg-cta-button__title">';
		$output.=  $title;
		$output.= '</span>';

		//
		// Suffix
		//
		if ( ! empty( $atts['suffix'] ) ) {
			$output.= ' <span class="issslpg-cta-button__suffix">';
			$output.= $atts['suffix'];
			$output.= '</span>';
		}

		//
		// Close
		//
		$output.= '</span><!-- /issslpg-cta-button-secondary -->';
		$output.= '</span><!-- /issslpg-cta-button-inner-wrapper -->';
		$output.= '</a>';

		//
		// Return
		//
		return $output;
	}

	public function faq( $atts, $content = null, $sc_name = '' ) {
		$atts = shortcode_atts( array(
			'htag' => 'h4',
		), $atts, 'faq' );

		$faq_items = ISSSLPG_Options::get_setting( 'iss_faq', false, 'iss_faq_settings' );

		if ( ! $faq_items ) {
			return;
		}

		$output = '<div class="issslpg-faq-list">';
		$use_htag = ( ! empty( $atts['htag'] ) && $atts['htag'] != 'p' );
		foreach ( $faq_items as $faq_item ) {
			$output.= $use_htag ? "<{$atts['htag']}>" : '<p><b>';
			$output.= $faq_item['question'];
			$output.= $use_htag ? "</{$atts['htag']}>" : '</p></b>';
			$output.= "<p>{$faq_item['answer']}</p>";
		}
		$output.= '</div>';

		return $output;
	}

	public function faq_accordion( $atts, $content = null, $sc_name = '' ) {
		$faq_items = ISSSLPG_Options::get_setting( 'iss_faq', false, 'iss_faq_settings' );

		if ( ! $faq_items ) {
			return;
		}

		ob_start();
		?>
        <div class="issslpg-faq-accordion">
			<?php foreach ( $faq_items as $faq_item ) : ?>
                <div class="issslpg-faq-accordion-item js-issslpg-accordion" data-status="closed">
                    <div class="issslpg-faq-accordion-item-header js-issslpg-accordion-trigger">
                        <div class="issslpg-faq-accordion-icon">
                            <span class="dashicons dashicons-arrow-right"></span>
                        </div>
                        <div class="issslpg-faq-accordion-title">
							<?php echo $faq_item['question']; ?>
                        </div>
                    </div>
                    <div class="issslpg-faq-accordion-item-body js-issslpg-accordion-target">
						<?php echo $faq_item['answer']; ?>
                    </div>
                </div>
			<?php endforeach; ?>
        </div>
        <script>
			(function( $ ) {
				'use strict';

				$(function() {
					setup_accordion();
				});

				/* FAQ Accordion
				 ========================================================================= */

				function setup_accordion() {
					var $all_triggers = $('.js-issslpg-accordion-trigger');
					var $all_targets = $('.js-issslpg-accordion-target');

					$all_triggers.click(function(e) {
						// If panel is closed...
						if( $(this).parent().attr('data-status') == 'closed' ) {
							// Close all panels
							$all_targets.each(function( index ) {
								$(this).parent().attr('data-status', 'closed');
								$(this).slideUp();
							});
							// Open panel
							$(this).next().slideDown();
							$(this).parent().attr('data-status', 'open');
						}
						// If panel is open...
						else {
							// Close panel
							$(this).next().slideUp();
							$(this).parent().attr('data-status', 'closed');
						}

						e.preventDefault();
					});
				}

			})( jQuery );
        </script>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

//		$output = '<div class="issslpg-faq-accordion">';
//		foreach ( $faq_items as $faq_item ) {
//			$output.= '<div class="issslpg-faq-accordion-item js-issslpg-accordion" data-status="closed">';
//			$output.=     '<div class="issslpg-faq-accordion-item-header js-issslpg-accordion-trigger">';
//			$output.=         '<div class="issslpg-faq-accordion-icon">';
//			$output.=             '<span class="dashicons dashicons-arrow-right"></span>';
//			$output.=         '</div>';
//			$output.=         '<div class="issslpg-faq-accordion-title">';
//			$output.=             $faq_item['question'];
//			$output.=         '</div>';
//			$output.=     '</div>';
//			$output.=     '<div class="issslpg-faq-accordion-item-body js-issslpg-accordion-target">';
//			$output.=         $faq_item['answer'];
//			$output.=         $str;
//			$output.=     '</div>';
//			$output.= '</div>';
//		}
//		$output.= '</div>';

		return $output;
	}

	public function sitemap( $atts, $content = null, $sc_name = '' ) {

		global $wp_query;

		$sitemap = new ISSSLPG_Public_Sitemap_Data();

		$output = '<h3>' . __( 'HTML Sitemap', 'issslpg' ) . '</h3>';

		if ( ( isset( $wp_query->query_vars['county_id'] ) && isset( $wp_query->query_vars['template_page_id'] ) ) ) {
//		if ( ( isset( $wp_query->query_vars['county_id'] ) && isset( $wp_query->query_vars['landing_page_id'] ) ) || ( isset( $wp_query->query_vars['state'] ) && isset( $wp_query->query_vars['county'] ) ) ) {
			$output.= $sitemap->get_landing_pages_list();
		}
        elseif ( isset( $wp_query->query_vars['template_page_id'] ) && isset( $wp_query->query_vars['state_id'] ) ) {
			$output.= $sitemap->get_counties_list();
		}
		// If state name is found in URL, get counties of that state
        elseif ( isset( $wp_query->query_vars['state_id'] ) || isset( $wp_query->query_vars['state'] ) ) {
			$output.= $sitemap->get_pages_list();
		}
		// If country name is found in URL, get states of that country
        elseif ( isset( $wp_query->query_vars['country_id'] ) || isset( $wp_query->query_vars['country'] ) ) {
			$output.= $sitemap->get_states_list();
		}
        elseif ( isset( $wp_query->query_vars['show_pages'] ) ) {
			$output.= $sitemap->get_pages_list();
		}
		// If no query vars are given, just show a list of all states
		else {
			$output.= $sitemap->get_country_list();
			$output.= '<h3>' . __( 'XML Sitemap', 'issslpg' ) . '</h3>';
			$output.= $sitemap->get_xml_sitemap_list();
		}

		return $output;
	}

	public function alt_text_page_title_city_state( $atts, $content = null, $sc_name = '' ) {
		if ( ! ISSSLPG_Landing_Page::is_landing_page() ) {
			return empty( $atts['default'] ) ? '' : $atts['default'];
		}

		$landing_page_id = ISSSLPG_Landing_Page::get_landing_page_id();
		$cached_random_number = null;
		if ( $this->cache_manager ) {
			$this->cache_manager->load_record( $landing_page_id );
			$cached_random_number = $this->cache_manager->get_current_record_entry_value( $sc_name );
		}

		if ( ! is_null( $cached_random_number ) ) {
			$random_number = $cached_random_number;
		} else {
			$random_number = mt_rand( 1, 9999 );
			if ( $this->cache_manager ) {
				$this->cache_manager->add_new_record_entry_value( $sc_name, $random_number );
			}
		}

		$template_page_id = get_post_meta( get_the_ID(), '_issslpg_template_page_id', true );
		$page_title = get_the_title( $template_page_id );

		$city = $this->city_data->name;
		$state = $this->city_data->state;

		$page_title_city_state_zip_code_county = $this->helper->join( [$page_title . ' in ' . $city, $state . " ({$random_number})"], $atts );
		$page_title_city_state_zip_code_county = $this->helper->add_prefix( $page_title_city_state_zip_code_county, $atts );

		return $page_title_city_state_zip_code_county;
	}

	public function alt_text_page_title_city_state_abbr( $atts, $content = null, $sc_name = '' ) {
		if ( ! ISSSLPG_Landing_Page::is_landing_page() ) {
			return empty( $atts['default'] ) ? '' : $atts['default'];
		}

		$landing_page_id = ISSSLPG_Landing_Page::get_landing_page_id();
		$cached_random_number = null;
		if ( $this->cache_manager ) {
			$this->cache_manager->load_record( $landing_page_id );
			$cached_random_number = $this->cache_manager->get_current_record_entry_value( $sc_name );
		}

		if ( ! is_null( $cached_random_number ) ) {
			$random_number = $cached_random_number;
		} else {
			$random_number = mt_rand( 1, 9999 );
			if ( $this->cache_manager ) {
				$this->cache_manager->add_new_record_entry_value( $sc_name, $random_number );
			}
		}

		$template_page_id = get_post_meta( get_the_ID(), '_issslpg_template_page_id', true );
		$page_title = get_the_title( $template_page_id );

		$city = $this->city_data->name;
		$state = $this->city_data->state_abbr;

		$page_title_city_state_zip_code_county = $this->helper->join( [$page_title . ' in ' . $city, $state . " ({$random_number})"], $atts );
		$page_title_city_state_zip_code_county = $this->helper->add_prefix( $page_title_city_state_zip_code_county, $atts );

		return $page_title_city_state_zip_code_county;
	}

	public function alt_text_page_title_city_state_zip_code_county( $atts, $content = null, $sc_name = '' ) {
		if ( ! ISSSLPG_Landing_Page::is_landing_page() ) {
			return empty( $atts['default'] ) ? '' : $atts['default'];
		}

		$landing_page_id = ISSSLPG_Landing_Page::get_landing_page_id();
		$cached_random_number = null;
		if ( $this->cache_manager ) {
			$this->cache_manager->load_record( $landing_page_id );
			$cached_random_number = $this->cache_manager->get_current_record_entry_value( $sc_name );
		}

		if ( ! is_null( $cached_random_number ) ) {
			$random_number = $cached_random_number;
		} else {
			$random_number = mt_rand( 1, 9999 );
			if ( $this->cache_manager ) {
				$this->cache_manager->add_new_record_entry_value( $sc_name, $random_number );
			}
		}

		$template_page_id = get_post_meta( get_the_ID(), '_issslpg_template_page_id', true );
		$page_title = get_the_title( $template_page_id );

		$city     = $this->city_data->name;
		$state    = $this->city_data->state;
		$zip_code = $this->city_data->zip_code;
		$county   = $this->city_data->county;

		$page_title_city_state_zip_code_county = $this->helper->join( [$page_title . ' in ' . $city, $state, $zip_code, $county . " ({$random_number})"], $atts );
		$page_title_city_state_zip_code_county = $this->helper->add_prefix( $page_title_city_state_zip_code_county, $atts );

		return $page_title_city_state_zip_code_county;
	}

	public function alt_text_page_title_city_state_abbr_zip_code_county( $atts, $content = null, $sc_name = '' ) {
		if ( ! ISSSLPG_Landing_Page::is_landing_page() ) {
			return empty( $atts['default'] ) ? '' : $atts['default'];
		}

		$landing_page_id = ISSSLPG_Landing_Page::get_landing_page_id();
		$cached_random_number = null;
		if ( $this->cache_manager ) {
			$this->cache_manager->load_record( $landing_page_id );
			$cached_random_number = $this->cache_manager->get_current_record_entry_value( $sc_name );
		}

		if ( ! is_null( $cached_random_number ) ) {
			$random_number = $cached_random_number;
		} else {
			$random_number = mt_rand( 1, 9999 );
			if ( $this->cache_manager ) {
				$this->cache_manager->add_new_record_entry_value( $sc_name, $random_number );
			}
		}

		$template_page_id = get_post_meta( get_the_ID(), '_issslpg_template_page_id', true );
		$page_title = get_the_title( $template_page_id );

		$city     = $this->city_data->name;
		$state    = $this->city_data->state_abbr;
		$zip_code = $this->city_data->zip_code;
		$county   = $this->city_data->county;

		$page_title_city_state_zip_code_county = $this->helper->join( [$page_title . ' in ' . $city, $state, $zip_code, $county . " ({$random_number})"], $atts );
		$page_title_city_state_zip_code_county = $this->helper->add_prefix( $page_title_city_state_zip_code_county, $atts );

		return $page_title_city_state_zip_code_county;
	}

	public function local_image_slider( $atts, $content = null, $sc_name = '' ) {
		return $this->slider( '_issslpg_local_images', 'image_id', $atts );
	}

	public function related_landing_pages( $atts, $content = null, $sc_name = '' ) {
		extract( shortcode_atts( array(
			'title' => __( 'Related Services', 'issslpg' ),
			'limit' => '10',
		), $atts ) );

		$landing_pages = ISSSLPG_Landing_Page_Api::get_related_landing_pages( (int)$limit, 'post-thumbnail', 55, 'iss_related_landing_pages' );

		// Return if there is no landing page data
		if ( empty( $landing_pages ) ) {
			return;
		}

		// Construct output
		$output = '';
		$output.= empty( $title ) ? "<h3>{$title}</h3>" : '';
		$output.= '<ul>';
		foreach( $landing_pages as $landing_page ) {
			$permalink  = $landing_page['permalink'];
			$page_title = $landing_page['title'];
			$output.= "<li><a href='{$permalink}'>{$page_title}</a></li>";
		}
		$output.= '</ul>';

		// Return output
		return $output;
	}

	public function dynamic_lp_content( $atts, $content = null, $sc_name = '' ) {
		$landing_page_id = ISSSLPG_Landing_Page::get_landing_page_id();
		$handle = str_replace( array( 'iss_lp_', '_content' ), '', $sc_name );
		$cached_content_id = null;
		if ( $this->cache_manager ) {
			$this->cache_manager->load_record( $landing_page_id );
			$cached_content_id = $this->cache_manager->get_current_record_entry_value( $sc_name );
		}

		if ( ! is_null( $cached_content_id ) ) {
			$random_content_id = $cached_content_id;
		} else {
			$pinned_content_block_number = get_post_meta( $landing_page_id, "_issslpg_pinned_{$handle}_content_block", true );
			if ( ! empty ( $pinned_content_block_number ) ) {
				$random_content_id = $pinned_content_block_number;
			} else {
				$random_content_id = $this->randomization->get_random_content_id( "_issslpg_{$handle}_content", 'content', $landing_page_id );
			}
			if ( $this->cache_manager ) {
				$this->cache_manager->add_new_record_entry_value( $sc_name, $random_content_id );
			}
		}

		return ISSSLPG_Meta_Data::get_processed_content( "_issslpg_{$handle}_content", 'content', $random_content_id, $landing_page_id );
	}

	public function dynamic_lp_image( $atts, $content = null, $sc_name = '' ) {
		$landing_page_id = ISSSLPG_Landing_Page::get_landing_page_id();
		$handle = str_replace( array( 'iss_lp_', '_image' ), '', $sc_name );
		$cached_image_id = null;
		if ( $this->cache_manager ) {
			$this->cache_manager->load_record( $landing_page_id );
			$cached_image_id = $this->cache_manager->get_current_record_entry_value( $sc_name );
		}

		extract( shortcode_atts( array(
			'size' => 'large',
			'class' => '',
		), $atts ) );

		if ( ! is_null( $cached_image_id ) ) {
			$random_image_id = $cached_image_id;
		} else {
			$no_duplicates = get_post_meta( $landing_page_id, "_issslpg_no_duplicate_{$handle}_images", true );
			if ( $no_duplicates ) {
				$random_image_id = $this->randomization->get_random_image_id_without_duplicates( "_issslpg_{$handle}_images", 'image_id', $landing_page_id );
			} else {
				$random_image_id = $this->randomization->get_random_image_id( "_issslpg_{$handle}_images", 'image_id', $landing_page_id );
			}
			if ( $this->cache_manager ) {
				$this->cache_manager->add_new_record_entry_value( $sc_name, $random_image_id );
			}
		}

		$image = wp_get_attachment_image( $random_image_id, $size, false, array( 'class' => $class, 'style' => 'max-width: 100%; height: auto;' ) );

		return "<figure class='wp-block-image'>{$image}</figure>";
	}

	public function dynamic_lp_image_slider( $atts, $content = null, $sc_name = '' ) {
		$handle = str_replace( array( 'iss_lp_', '_image_slider' ), '', $sc_name );
		return $this->slider( "_issslpg_{$handle}_images", 'image_id', $atts );
	}

	public function dynamic_lp_singular_keyword( $atts, $content = null, $sc_name = '' ) {
		$landing_page_id = ISSSLPG_Landing_Page::get_landing_page_id();
		$handle = str_replace( 'iss_lp_singular_', '', $sc_name );
		$cached_keyword_id = null;
		if ( $this->cache_manager ) {
			$this->cache_manager->load_record( $landing_page_id );
			$cached_keyword_id = $this->cache_manager->get_current_record_entry_value( $sc_name );
		}
		$text = get_post_meta( $landing_page_id, "_issslpg_singular_{$handle}_keywords", true );

		if ( ! is_null( $cached_keyword_id ) ) {
			$random_line_id = $cached_keyword_id;
		} else {
			$no_duplicates = get_post_meta( $landing_page_id, "_issslpg_no_duplicate_{$handle}_keywords", true );
			if ( $no_duplicates ) {
				$random_line_id = $this->randomization->get_random_line_id_from_text_without_duplicates( "_issslpg_singular_{$handle}_keywords", $text );
			} else {
				$random_line_id = $this->randomization->get_random_line_id_from_text( "_issslpg_singular_{$handle}_keywords", $text );
			}
			if ( $this->cache_manager ) {
				$this->cache_manager->add_new_record_entry_value( $sc_name, $random_line_id );
			}
		}

		$random_line = $this->randomization->get_line_from_text( $text, $random_line_id );
		$random_line = $this->helper->add_prefix( $random_line, $atts );
		return $random_line;
	}

	public function dynamic_lp_plural_keyword( $atts, $content = null, $sc_name = '' ) {
		$landing_page_id = ISSSLPG_Landing_Page::get_landing_page_id();
		$handle = str_replace( 'iss_lp_plural_', '', $sc_name );
		$cached_keyword_id = null;
		if ( $this->cache_manager ) {
			$this->cache_manager->load_record( $landing_page_id );
			$cached_keyword_id = $this->cache_manager->get_current_record_entry_value( $sc_name );
		}
		$text = get_post_meta( $landing_page_id, "_issslpg_plural_{$handle}_keywords", true );

		if ( ! is_null( $cached_keyword_id ) ) {
			$random_line_id = $cached_keyword_id;
		} else {
			$no_duplicates = get_post_meta( $landing_page_id, "_issslpg_no_duplicate_{$handle}_keywords", true );
			if ( $no_duplicates ) {
				$random_line_id = $this->randomization->get_random_line_id_from_text_without_duplicates( "_issslpg_plural_{$handle}_keywords", $text );
			} else {
				$random_line_id = $this->randomization->get_random_line_id_from_text( "_issslpg_plural_{$handle}_keywords", $text );
			}
			if ( $this->cache_manager ) {
				$this->cache_manager->add_new_record_entry_value( $sc_name, $random_line_id );
			}
		}

		$random_line = $this->randomization->get_line_from_text( $text, $random_line_id );
		$random_line = $this->helper->add_prefix( $random_line, $atts );
		return $random_line;
	}

	public function dynamic_lp_phrase( $atts, $content = null, $sc_name = '' ) {
		$landing_page_id = ISSSLPG_Landing_Page::get_landing_page_id();
		$handle = str_replace( array( 'iss_lp_', '_phrase' ), '', $sc_name );

		$cached_phrase_id = null;
		if ( $this->cache_manager ) {
			$this->cache_manager->load_record( $landing_page_id );
			$cached_phrase_id = $this->cache_manager->get_current_record_entry_value( $sc_name );
		}
		$text = get_post_meta( $landing_page_id, "_issslpg_{$handle}_phrases", true );

		if ( ! is_null( $cached_phrase_id ) ) {
			$random_line_id = $cached_phrase_id;
		} else {
			$no_duplicates = get_post_meta( $landing_page_id, "_issslpg_no_duplicate_{$handle}_phrases", true );
			if ( $no_duplicates ) {
				$random_line_id = $this->randomization->get_random_line_id_from_text_without_duplicates( "_issslpg_{$handle}_phrases", $text );
			} else {
				$random_line_id = $this->randomization->get_random_line_id_from_text( "_issslpg_{$handle}_phrases", $text );
			}
			if ( $this->cache_manager ) {
				$this->cache_manager->add_new_record_entry_value( $sc_name, $random_line_id );
			}
		}

		$random_line = $this->randomization->get_line_from_text( $text, $random_line_id );
		$random_line = $this->helper->add_prefix( $random_line, $atts );
		// This is a fix to enable the user to put shortcodes within shortcodes within shortcodes.
		return do_shortcode( $random_line );
	}

	private function slider( $group_id, $field_id, $atts ) {
		if ( ! ISSSLPG_Landing_Page::get_landing_page_id() ) {
			return false;
		}

		extract( shortcode_atts( array(
			'auto' => '',
			'size' => 'large',
		), $this->helper->normalize_empty_atts( $atts ) ) );

		$output = '';

		$config_sideshow = $auto ? 'true' : 'false';

		$config = <<<EOT
			<script>
				(function( $ ) {
					if ($.isFunction(jQuery.fn.flexslider)) {
						$('.flexslider').flexslider( {
							namespace: "flex-",             //{NEW} String: Prefix string attached to the class of every element generated by the plugin
							selector: ".slides > li",       //{NEW} Selector: Must match a simple pattern. '{container} > {slide}' -- Ignore pattern at your own peril
							animation: "fade",              //String: Select your animation type, "fade" or "slide"
							easing: "swing",                //{NEW} String: Determines the easing method used in jQuery transitions. jQuery easing plugin is supported!
							direction: "horizontal",        //String: Select the sliding direction, "horizontal" or "vertical"
							reverse: false,                 //{NEW} Boolean: Reverse the animation direction
							animationLoop: true,            //Boolean: Should the animation loop? If false, directionNav will received "disable" classes at either end
							smoothHeight: false,            //{NEW} Boolean: Allow height of the slider to animate smoothly in horizontal mode
							startAt: 0,                     //Integer: The slide that the slider should start on. Array notation (0 = first slide)
							slideshow: $config_sideshow,    //Boolean: Animate slider automatically
							slideshowSpeed: 3000,           //Integer: Set the speed of the slideshow cycling, in milliseconds
							animationSpeed: 600,            //Integer: Set the speed of animations, in milliseconds
							initDelay: 0,                   //{NEW} Integer: Set an initialization delay, in milliseconds
							randomize: false,               //Boolean: Randomize slide order
							// Usability features
							pauseOnAction: true,            //Boolean: Pause the slideshow when interacting with control elements, highly recommended.
							pauseOnHover: false,            //Boolean: Pause the slideshow when hovering over slider, then resume when no longer hovering
							useCSS: true,                   //{NEW} Boolean: Slider will use CSS3 transitions if available
							touch: true,                    //{NEW} Boolean: Allow touch swipe navigation of the slider on touch-enabled devices
							video: false,                   //{NEW} Boolean: If using video in the slider, will prevent CSS3 3D Transforms to avoid graphical glitches
							// Primary Controls
							controlNav: false,               //Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
							directionNav: false,             //Boolean: Create navigation for previous/next navigation? (true/false)
							prevText: "Previous",           //String: Set the text for the "previous" directionNav item
							nextText: "Next",               //String: Set the text for the "next" directionNav item
							// Secondary Navigation
							keyboard: true,                 //Boolean: Allow slider navigating via keyboard left/right keys
							multipleKeyboard: false,        //{NEW} Boolean: Allow keyboard navigation to affect multiple sliders. Default behavior cuts out keyboard navigation with more than one slider present.
							mousewheel: false,              //{UPDATED} Boolean: Requires jquery.mousewheel.js (https://github.com/brandonaaron/jquery-mousewheel) - Allows slider navigating via mousewheel
							pausePlay: false,               //Boolean: Create pause/play dynamic element
							pauseText: 'Pause',             //String: Set the text for the "pause" pausePlay item
							playText: 'Play',               //String: Set the text for the "play" pausePlay item
							// Special properties
							controlsContainer: "",          //{UPDATED} Selector: USE CLASS SELECTOR. Declare which container the navigation elements should be appended too. Default container is the FlexSlider element. Example use would be ".flexslider-container". Property is ignored if given element is not found.
							manualControls: "",             //Selector: Declare custom control navigation. Examples would be ".flex-control-nav li" or "#tabs-nav li img", etc. The number of elements in your controlNav should match the number of slides/tabs.
							sync: "",                       //{NEW} Selector: Mirror the actions performed on this slider with another slider. Use with care.
							asNavFor: "",                   //{NEW} Selector: Internal property exposed for turning the slider into a thumbnail navigation for another slider
							// Carousel Options
							itemWidth: 0,                   //{NEW} Integer: Box-model width of individual carousel items, including horizontal borders and padding.
							itemMargin: 0,                  //{NEW} Integer: Margin between carousel items.
							minItems: 0,                    //{NEW} Integer: Minimum number of carousel items that should be visible. Items will resize fluidly when below this.
							maxItems: 0,                    //{NEW} Integer: Maxmimum number of carousel items that should be visible. Items will resize fluidly when above this limit.
							move: 0,                        //{NEW} Integer: Number of carousel items that should move on animation. If 0, slider will move all visible items.
							// Callback API
							start: function (slider) {
								slider.container.click(function () {
									if (!slider.animating) {
										slider.flexAnimate(slider.getTarget('next'));
									}
								});
							},            //Callback: function(slider) - Fires when the slider loads the first slide
							before: function () {
							},           //Callback: function(slider) - Fires asynchronously with each slider animation
							after: function () {
							},            //Callback: function(slider) - Fires after each slider animation completes
							end: function () {
							},              //Callback: function(slider) - Fires when the slider reaches the last slide (asynchronous)
							added: function () {
							},            //{NEW} Callback: function(slider) - Fires after a slide is added
							removed: function () {
							}           //{NEW} Callback: function(slider) - Fires after a slide is removed
						} );
					}
				})( jQuery );
			</script>
EOT;

		$landing_page_id = ISSSLPG_Landing_Page::get_landing_page_id();
		$image_ids = ISSSLPG_Meta_Data::get_group_fields( $group_id, $field_id, $landing_page_id );

		if ( ! empty ( $image_ids ) ) {

//			$image_ids = ISSSLPG_Helpers::reduce_array_by_rows_limit( $image_ids, 2 );
			shuffle( $image_ids );

			// Output gallery slider markup and images
			$output .= "<div class='flexslider'>";
			$output .= '<ul class="slides">';
			foreach ( $image_ids as $image_id ) {
				$output .= '<li>' . wp_get_attachment_image( $image_id, $size ) . '</li>';
			}
			$output .= '</ul>';
			$output .= '</div>';

			$output .= $config;
		}

		return $output;
	}

	public function demographics_city_type( $atts, $content = null, $sc_name = '' ) {
		if ( ! ISSSLPG_Helpers::is_demographics_usage_allowed() ) {
			return;
		}

		return $this->helper->add_prefix( $this->location->get_city_type(), $atts );
	}

	public function demographics_geo_id( $atts, $content = null, $sc_name = '' ) {
		if ( ! ISSSLPG_Helpers::is_demographics_usage_allowed() ) {
			return;
		}

		return $this->helper->add_prefix( $this->location->get_geo_id(), $atts );
	}

	public function demographics_population( $atts, $content = null, $sc_name = '' ) {
		if ( ! ISSSLPG_Helpers::is_demographics_usage_allowed() ) {
			return;
		}

		$population = number_format( $this->location->get_population() );
		return $this->helper->add_prefix( $population, $atts );
	}

	public function demographics_households( $atts, $content = null, $sc_name = '' ) {
		if ( ! ISSSLPG_Helpers::is_demographics_usage_allowed() ) {
			return;
		}

		$households = number_format( $this->location->get_households() );
		return $this->helper->add_prefix( $households, $atts );
	}

	public function demographics_median_income( $atts, $content = null, $sc_name = '' ) {
		if ( ! ISSSLPG_Helpers::is_demographics_usage_allowed() ) {
			return;
		}

		$median_income = $this->location->get_median_income();
		$median_income = number_format( $median_income );
		return $this->helper->add_prefix( $median_income, $atts );
	}

	public function demographics_land_area( $atts, $content = null, $sc_name = '' ) {
		if ( ! ISSSLPG_Helpers::is_demographics_usage_allowed() ) {
			return;
		}

		$land_area = number_format( $this->location->get_land_area() );
		return $this->helper->add_prefix( $land_area, $atts );
	}

	public function demographics_water_area( $atts, $content = null, $sc_name = '' ) {
		if ( ! ISSSLPG_Helpers::is_demographics_usage_allowed() ) {
			return;
		}

		$water_area = number_format( $this->location->get_land_area() );
		return $this->helper->add_prefix( $water_area, $atts );
	}

	public function demographics_latitude( $atts, $content = null, $sc_name = '' ) {
		if ( ! ISSSLPG_Helpers::is_demographics_usage_allowed() ) {
			return;
		}

		return $this->helper->add_prefix( $this->location->get_latitude(), $atts );
	}

	public function demographics_longitude( $atts, $content = null, $sc_name = '' ) {
		if ( ! ISSSLPG_Helpers::is_demographics_usage_allowed() ) {
			return;
		}

		return $this->helper->add_prefix( $this->location->get_longitude(), $atts );
	}

	public function demographics_climate_data( $atts, $content = null, $sc_name = '' ) {
		if ( ! ISSSLPG_Helpers::is_demographics_usage_allowed() ) {
			return;
		}

		$climate_data = $this->location->get_climate_data();

		if (! $climate_data) {
			return;
		}

		// Shuffle
		$climate_data = ISSSLPG_Array_Helpers::shuffle_associative_array( $climate_data );
		list( $climate_data_handle ) = array_keys( $climate_data );
		$random_dataset = reset( $climate_data );

		// Assign titles to data array keys
		$category_titles = array(
			'snowfall'              => __( 'Snowfall', 'rvn' ),
			'max_temperature'       => __( 'Maximum Temperature', 'rvn' ),
			'min_temperature'       => __( 'Minimum Temperature', 'rvn' ),
			'avg_temperature'       => __( 'Average Temperature', 'rvn' ),
			'precipitation_normals' => __( 'Precipitation Normals', 'rvn' ),
		);
		$date_titles = array(
			'jan' => __( 'January', 'rvn' ),
			'feb' => __( 'February', 'rvn' ),
			'mar' => __( 'March', 'rvn' ),
			'apr' => __( 'April', 'rvn' ),
			'may' => __( 'May', 'rvn' ),
			'jun' => __( 'June', 'rvn' ),
			'jul' => __( 'July', 'rvn' ),
			'aug' => __( 'August', 'rvn' ),
			'sep' => __( 'September', 'rvn' ),
			'oct' => __( 'October', 'rvn' ),
			'nov' => __( 'November', 'rvn' ),
			'dec' => __( 'December', 'rvn' ),
			'ann' => __( 'Annual', 'rvn' ),
		);

		// Output
		$output = "<b>{$category_titles[$climate_data_handle]}</b><ul>";
		foreach ( $random_dataset as $date_handle => $value ) :
			$output.= "<li><b>{$date_titles[$date_handle]}:</b> {$value}</li>";
		endforeach;
		$output.= '</ul>';

		return $output;
//		return $this->helper->add_prefix( $output, $atts );
	}

	public function demographics_crime_data( $atts, $content = null, $sc_name = '' ) {
		if ( ! ISSSLPG_Helpers::is_demographics_usage_allowed() ) {
			return;
		}

		$crime_data = $this->location->get_crime_data();

		if ( empty( $crime_data ) ) {
			return;
		}

		// Filter / Shuffle
		$crime_data = ISSSLPG_Array_Helpers::remove_empty_values( $crime_data );
		$crime_data = ISSSLPG_Array_Helpers::shuffle_associative_array( $crime_data );

		// Assign titles to data array keys
		$category_titles = array(
			'robbery'                 => __( 'Robbery', 'issslpg' ),
			'motor_vehicle_theft'     => __( 'Motor Vehicle Theft', 'issslpg' ),
			'property_crime'          => __( 'Property Crime', 'issslpg' ),
			'aggravated_assault'      => __( 'Aggravated Assault', 'issslpg' ),
			'arson'                   => __( 'Arson', 'issslpg' ),
			'rape'                    => __( 'Rape', 'issslpg' ),
			'burglary'                => __( 'Burglary', 'issslpg' ),
			'violent_crime'           => __( 'Violent Crime', 'issslpg' ),
			'larceny_theft'           => __( 'Larceny Theft', 'issslpg' ),
			'murder_and_manslaughter' => __( 'Murder and Manslaughter', 'issslpg' ),
		);

		// Output
		$output = '<ul>';
		foreach ( $crime_data as $date_handle => $value ) :
			$value = number_format( $value );
			$output.="<li><b>{$category_titles[$date_handle]}:</b> {$value}</li>";
		endforeach;
		$output.='</ul>';


//		var_dump( $this->location->get_fbi_data() );
		return $output;
//		return $this->helper->add_prefix( $crime_data, $atts );
	}

	public function local_office_street( $atts, $content = null, $sc_name = '' ) {
		return ISSSLPG_Landing_Page_Api::get_county_settings( 'office_street' );
	}

	public function local_office_city( $atts, $content = null, $sc_name = '' ) {
		return ISSSLPG_Landing_Page_Api::get_county_settings( 'office_city' );
	}

	public function local_office_zip_code( $atts, $content = null, $sc_name = '' ) {
		return ISSSLPG_Landing_Page_Api::get_county_settings( 'office_zip_code' );
	}

	public function local_office_address( $atts, $content = null, $sc_name = '' ) {
		$settings = ISSSLPG_Landing_Page_Api::get_county_settings();
		if ( isset( $settings['office_street'] ) && isset( $settings['office_city'] ) && isset( $settings['office_zip_code'] ) ) {
			return "<address>{$settings['office_street']}<br>{$settings['office_city']}, {$settings['office_zip_code']}</address>";
		}
	}

}