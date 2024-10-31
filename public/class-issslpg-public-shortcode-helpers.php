<?php



class ISSSLPG_Public_Shortcode_Helpers {

	public function get_prefix( $atts ) {
		if ( isset( $atts['prefix'] ) && ! empty( $atts['prefix'] ) ) {
			return $atts['prefix'] . ' ';
		}

		return '';
	}

	public function get_suffix( $atts ) {
		if ( isset( $atts['suffix'] ) && ! empty( $atts['suffix'] ) ) {
			return ' ' . $atts['suffix'];
		}

		return '';
	}

	public function add_prefix( $content, $atts ) {
		if ( ! empty ( $content ) ) {
			$prefix = $this->get_prefix( $atts );
			return $prefix . $content;
		}

		return $content;
	}

	public function add_suffix( $content, $atts ) {
		if ( ! empty ( $content ) ) {
			$suffix = $this->get_suffix( $atts );
			return $content . $suffix;
		}

		return $content;
	}

	public function add_prefix_and_suffix( $content, $atts ) {
		if ( ! empty ( $content ) ) {
			$suffix = $this->get_suffix( $atts );
			$prefix = $this->get_prefix( $atts );
			return $prefix . $content . $suffix;
		}

		return $content;
	}

	public function join( $pieces, $atts ) {

		if ( ! empty( $pieces ) && is_array( $pieces ) ) {
			$pieces = array_values( array_filter( ( $pieces ) ) );
			if ( isset( $atts['join'] ) && ! empty( $atts['join'] ) ) {
				$glue = $atts['join'];
				return join( $glue, $pieces );
			}

			return join( ', ', $pieces );
		}

		return $pieces;
	}

	public function limit( $data, $atts, $default = false ) {
		if ( ! is_array( $data ) ) {
			return $data;
		}

		$limit = $default;
		if ( isset( $atts['limit'] ) && ! empty( $atts['limit'] ) ) {
			$limit = intval( $atts['limit'] );
		}

		if ( is_int( $limit ) ) {
			return array_slice( $data, 0, $limit );
		}

		return $data;
	}

	static public function normalize_empty_atts( $atts ) {
		if ( ! empty( $atts ) ) {
			foreach ( $atts as $attribute => $value ) {
				if ( is_int( $attribute ) ) {
					$atts[ strtolower( $value ) ] = true;
					unset( $atts[ $attribute ] );
				}
			}
		}

		return $atts;
	}

	static public function get_dynamic_shortcode_data() {

		$tags = array();
		$shortcode_data = array();

		// Content Shortcodes
		$content_panels = ISSSLPG_Options::get_panels( 'landing_page_content_panels' );
		foreach ( $content_panels as $content_panel ) {
			$title = $content_panel['title'];
			$handle = $content_panel['handle'];
			$tag = "iss_lp_{$handle}_content";
			if ( ! in_array( $tag, $tags ) ) {
				$tags[] = $tag;
				$shortcode_data['content'][$tag]['title']    = "{$title} Content";
				$shortcode_data['content'][$tag]['tag']      = $tag;
				$shortcode_data['content'][$tag]['callback'] = 'dynamic_lp_content';
			}
		}

		// Image Shortcodes
		$image_panels = ISSSLPG_Options::get_panels( 'landing_page_image_panels' );
		foreach ( $image_panels as $image_panel ) {
			$title = $image_panel['title'];
			$handle = $image_panel['handle'];
			// Dynamic Image Shortcode
			$tag = "iss_lp_{$handle}_image";
			if ( ! in_array( $tag, $tags ) ) {
				$tags[] = $tag;
				$shortcode_data['image'][$tag]['title']    = "{$title} Image";
				$shortcode_data['image'][$tag]['tag']      = $tag;
				$shortcode_data['image'][$tag]['callback'] = 'dynamic_lp_image';
			}
			// Dynamic Image Slider Shortcode
			$tag = "iss_lp_{$handle}_image_slider";
			if ( ! in_array( $tag, $tags ) ) {
				$tags[] = $tag;
				$shortcode_data['image_slider'][$tag]['title']    = "{$title} Slider";
				$shortcode_data['image_slider'][$tag]['tag']      = $tag;
				$shortcode_data['image_slider'][$tag]['callback'] = 'dynamic_lp_image_slider';
			}
		}

		// Keyword Shortcodes
		$keyword_panels = ISSSLPG_Options::get_panels( 'landing_page_keyword_panels' );
		foreach ( $keyword_panels as $keyword_panel ) {
			$title = $keyword_panel['title'];
			$handle = $keyword_panel['handle'];
			// Dynamic Singular Keyword Shortcode
			$tag = "iss_lp_singular_{$handle}";
			if ( ! in_array( $tag, $tags ) ) {
				$tags[] = $tag;
				$shortcode_data['singular_keyword'][$tag]['title']    = "Singular {$title} Keyword";
				$shortcode_data['singular_keyword'][$tag]['tag']      = $tag;
				$shortcode_data['singular_keyword'][$tag]['callback'] = 'dynamic_lp_singular_keyword';
			}
			// Dynamic Plural Keyword Shortcode
			$tag = "iss_lp_plural_{$handle}";
			if ( ! in_array( $tag, $tags ) ) {
				$tags[] = $tag;
				$shortcode_data['plural_keyword'][$tag]['title']    = "Plural {$title} Keyword";
				$shortcode_data['plural_keyword'][$tag]['tag']      = $tag;
				$shortcode_data['plural_keyword'][$tag]['callback'] = 'dynamic_lp_plural_keyword';
			}
		}

		// Phrase Shortcodes
		$phrase_panels = ISSSLPG_Options::get_panels( 'landing_page_phrase_panels' );
		foreach ( $phrase_panels as $phrase_panel ) {
			$title = $phrase_panel['title'];
			$handle = $phrase_panel['handle'];
			$tag = "iss_lp_{$handle}_phrase";
			if ( ! in_array( $tag, $tags ) ) {
				$tags[] = $tag;
				$shortcode_data['phrase'][$tag]['title']    = "{$title} Phrase";
				$shortcode_data['phrase'][$tag]['tag']      = $tag;
				$shortcode_data['phrase'][$tag]['callback'] = 'dynamic_lp_phrase';
			}
		}

		return $shortcode_data;
	}

}