<?php

// Resources:
// https://wordpress.stackexchange.com/questions/15218/how-to-generate-update-a-xml-sitemap-without-plugins

class ISSSLPG_Public_XML_Sitemap_Generator {

	public $sitemap_dir;

	public $sitemap_url;

	public function __construct() {
		$upload_dir = wp_upload_dir();
		$this->sitemap_dir = $upload_dir['basedir'] . '/sitemaps';
		$this->sitemap_url = $upload_dir['baseurl'] . '/sitemaps';
	}

	public function create_sitemaps() {
		if ( ! ISSSLPG_Options::get_xml_sitemap_setting( 'activate_xml_sitemaps', true ) ) {
			$this->delete_all_sitemaps();
			return;
		}

		if ( ISSSLPG_Options::get_xml_sitemap_setting( 'regenerate_xml_sitemaps' ) ) {
			$this->delete_all_sitemaps();
			for ( $i = 0; $i < $this->get_sitemap_count(); $i++ ) {
				$this->create_all_sitemap();
				$this->set_next_scheduled_sitemap_name();
			}
			ISSSLPG_Options::set_xml_sitemap_setting( 'regenerate_xml_sitemaps', 'off' );
		}
		else {
			$this->create_all_sitemap();
			$this->set_next_scheduled_sitemap_name();
		}
	}

	private function create_all_sitemap() {
		$this->create_post_type_sitemaps();
		$this->create_template_page_sitemaps();
		$this->create_index_sitemap();
	}

	private function create_index_sitemap() {
		$xml = $this->get_index_xml();
		$this->create_xml_file( 'sitemap_index', $xml );
	}

//	private function get_sitemap_info() {
//		$names = $this->get_sitemap_names();
//	}

	public function get_sitemap_count() {
		return count( $this->get_sitemap_names() );
	}

	public function get_post_type_sitemap_names( $include_suffix = false ) {
		// Get post type names
		$post_type_names = array();
		$post_types = $this->get_post_types();
		foreach ( $post_types as $post_type ) {
			$post_type_name = $post_type;
			if ( $include_suffix ) {
				$post_type_name = $post_type_name . '-sitemap';
			}
			$post_type_names[] = $post_type_name;
		}

		return $post_type_names;
	}

	public function get_template_page_sitemap_names( $include_suffix = false ) {
		// Get template page names
		$template_page_names = array();
		$template_pages = get_posts( array(
				'post_type'      => 'issslpg-template',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
		) );
		foreach( $template_pages as $template_page ) {
			if ( ISSSLPG_Options::get_xml_sitemap_setting( "xml_sitemaps_include_template_page_{$template_page->ID}", true ) ) {
				$template_page_name = $template_page->post_name;
				if ( $include_suffix ) {
					$template_page_name = $template_page_name . '-sitemap';
				}
				$template_page_names[] = $template_page_name;
			}
		}
		wp_reset_postdata();

		return $template_page_names;
	}

	public function get_sitemap_names( $include_suffix = false ) {
		$template_page_names = $this->get_template_page_sitemap_names( $include_suffix );
		$post_type_names = $this->get_post_type_sitemap_names( $include_suffix );

		$sitemap_names = array_merge( $post_type_names, $template_page_names );

		return $sitemap_names;
	}

	private function get_scheduled_sitemap_name() {
		$sitemap_names = $this->get_sitemap_names();
		$scheduled_sitemap_name = get_option( 'issslpg_next_scheduled_sitemap' );
		if ( $scheduled_sitemap_name ) {
			$scheduled_sitemap_id = array_search( $scheduled_sitemap_name, $sitemap_names );
			if ( $scheduled_sitemap_id !== false ) {
				return $scheduled_sitemap_name;
			}
		}

		return $sitemap_names[0];
	}

	private function set_next_scheduled_sitemap_name() {
		$sitemap_names = $this->get_sitemap_names();
		$current_sitemap_name = get_option( 'issslpg_next_scheduled_sitemap' );
		if ( $current_sitemap_name ) {
			$current_sitemap_id = array_search( $current_sitemap_name, $sitemap_names );
			if ( $current_sitemap_id !== false ) {
				$next_sitemap_id = $current_sitemap_id + 1;
				if ( isset( $sitemap_names[$next_sitemap_id] ) ) {
					update_option( 'issslpg_next_scheduled_sitemap', $sitemap_names[$next_sitemap_id] );
					return $sitemap_names[$next_sitemap_id];
				}
			}
		}

		update_option( 'issslpg_next_scheduled_sitemap', $sitemap_names[0] );
		return $sitemap_names[0];
	}

	private function create_template_page_sitemaps() {
		$county_limit = ISSSLPG_Helpers::get_county_limit();
		$used_county_ids = array();

		$template_pages = get_posts( array(
			'post_type'      => 'issslpg-template',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		) );
		$template_pages_data = array();
		foreach( $template_pages as $template_page ) {
			if ( ISSSLPG_Options::get_xml_sitemap_setting( "xml_sitemaps_include_template_page_{$template_page->ID}", true ) ) {
				$template_pages_data[ $template_page->ID ] = $template_page->post_name;
			}
		}
		wp_reset_postdata();

		foreach( $template_pages_data as $template_page_id => $template_page_slug ) {
			if ( $template_page_slug === $this->get_scheduled_sitemap_name() ) {
				$landing_pages = get_posts( array(
						'post_type'      => 'issslpg-landing-page',
						'post_status'    => 'publish',
						'orderby'        => 'rand',
						'posts_per_page' => -1,
						'meta_key'       => '_issslpg_template_page_id',
						'meta_value'     => $template_page_id,
				) );
				$xml = $this->get_header();
				foreach( $landing_pages as $landing_page ) {
					$county_id = get_post_meta( $landing_page->ID, '_issslpg_county_id', true );
					// Check if the number of used counties doesn't exceed the
					// county limit and then add county to list of used
					// counties, only if county wan't already added.
					if ( count( $used_county_ids ) < (int)$county_limit ) {
						if ( ! in_array( $county_id, $used_county_ids ) ) {
							$used_county_ids[] = $county_id;
						}
					}
					// If county is in used county list, add the entry for
					// landing page.
					if ( in_array( $county_id, $used_county_ids ) ) {
//						$county = new ISSSLPG_County_Data( $county_id );
						$xml .= $this->get_entry( get_the_permalink( $landing_page->ID ) );
					}
				}
				$xml.= $this->get_footer();
				$this->create_xml_file( $template_page_slug . '-sitemap', $xml );
			}
		}
		wp_reset_postdata();
	}

	private function create_post_type_sitemaps() {
		// Post Types
		$post_types = $this->get_post_types();
//		unset( $post_types['issslpg-landing-page'] );
		foreach ( $post_types as $post_type ) {
			if ( $post_type === $this->get_scheduled_sitemap_name() ) {
				$xml = $this->get_post_type_xml( $post_type );
				if ( $xml ) {
					$this->create_xml_file( $post_type . '-sitemap', $xml );
				}
			}
		}
	}

	private function delete_all_sitemaps() {
		$files = glob( $this->sitemap_dir . "/*" );
		foreach ( $files as $file ) {
			@unlink( $file );
		}
	}

//	private function delete_sitemaps() {
//		$file_names = $this->get_all_sitemap_file_names();
//		foreach ( $file_names as $file_name ) {
//			$this->delete_xml_file( $file_name );
//		}
//	}

	private function get_all_sitemap_file_names( $include_index_file = true ) {
		$file_names = array();

		$post_types = $this->get_post_types();
		foreach ( $post_types as $post_type ) {
			$file_names[] =  $post_type . '-sitemap';
		}

		if ( $include_index_file ) {
			$file_names[] = 'sitemap_index';
		}

		return $file_names;
	}

	private function delete_xml_file( $file_name ) {
		@unlink( $this->sitemap_dir . "/{$file_name}.xml" );
	}

	private function get_post_types() {
		$post_types = get_post_types( array(
			'public' => true,
		), 'names' );

		// Throw out post types we don't need (templates and landing pages are processed elsewhere)
		unset( $post_types['attachment'] );
		unset( $post_types['issslpg-template'] );
		unset( $post_types['issslpg-landing-page'] );
		unset( $post_types['issslpg-local'] );

		// Go through each post type and check if it's active in the settings
		// and if it actually contains posts
		foreach ( $post_types as $post_type ) {
			$post_count = wp_count_posts( $post_type );
			$post_count = (int)$post_count->publish;
			$setting_active = ISSSLPG_Options::get_xml_sitemap_setting( "xml_sitemaps_include_post_type_{$post_type}", true );
			if ( ! $setting_active || $post_count === 0 ) {
				unset( $post_types[$post_type] );
			}
		}

		return $post_types;
	}

	private function get_index_xml() {
		$xml = $this->get_index_header();

		$file_names = $this->get_sitemap_names( true );
		foreach ( $file_names as $file_name ) {
			$xml.= $this->get_index_entry( $file_name );
		}
		$xml.= $this->get_index_footer();

		return $xml;
	}

	private function get_index_entry( $file_name ) {
		$url = "{$this->sitemap_url}/{$file_name}.xml";

		$output = "\t" . '<sitemap>' . "\n";
		$output.= "\t\t" . '<loc>' . $url . '</loc>' . "\n";
		$output.= "\t" . '</sitemap>' . "\n";

		return $output;
	}

	private function get_landing_pages_xml( $template_page_id ) {

	}

	private function get_post_type_xml( $post_type ) {
		$post_type_entries = $this->get_post_type_entries( $post_type );
//
//		if( empty( $post_type_entries ) ) {
//			return false;
//		}

		$xml = $this->get_header();
		$xml.= $post_type_entries;
		$xml.= $this->get_footer();

		return $xml;
	}

	private function create_xml_file( $file_name, $xml ) {
		if ( ! file_exists( $this->sitemap_dir ) ) {
			mkdir( $this->sitemap_dir, 0777, true );
		}
		$this->delete_xml_file( $file_name );
		$fp = fopen( $this->sitemap_dir . "/{$file_name}.xml", 'w' ) or die( 'Unable to open file!' );
		fwrite( $fp, $xml );
		fclose( $fp );
	}

	private function get_header() {
		$output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$output.= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

		return $output;
	}

	private function get_index_header() {
		$output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$output.= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

		return $output;
	}

	private function get_footer() {
		$output = '</urlset>' . "\n";

		return $output;
	}

	private function get_index_footer() {
		$output = '</sitemapindex>' . "\n";

		return $output;
	}

	private function get_entry( $loc, $changefreq = 'weekly' ) {
		$output = "\t" . '<url>' . "\n";
		$output.= "\t\t" . '<loc>' . $loc . '</loc>' . "\n";
		$output.= "\t\t" . '<changefreq>' . $changefreq . '</changefreq>' . "\n";
		$output.= "\t" . '</url>' . "\n";

		return $output;
	}

	private function get_post_type_entries( $post_type ) {
		$output = '';

		$wp_query = new WP_Query( array(
				'post_type'      => $post_type,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
		) );

		if ( $wp_query->have_posts() ) {
			while ( $wp_query->have_posts() ) : $wp_query->the_post();
				$output.= $this->get_entry( get_the_permalink() );
			endwhile;
		}
		wp_reset_postdata();

		return $output;
	}

}