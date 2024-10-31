<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://intellasoftplugins.com
 * @since      1.0.0
 *
 * @package    ISSSLPG
 * @subpackage ISSSLPG/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    ISSSLPG
 * @subpackage ISSSLPG/public
 * @author     Ruven Pelka <ruven.pelka@gmail.com>
 */
class ISSSLPG_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->load_dependencies();
	}

	/**
	 * Load the required dependencies for this class.
	 *
	 * @since    1.0.0
	 */
	private function load_dependencies() {
//		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-issslpg-public-page-meta.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-issslpg-public-location.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-issslpg-public-shortcodes.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-issslpg-public-shortcode-helpers.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-issslpg-public-sitemap-data.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-issslpg-public-routing.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-issslpg-public-schema.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-issslpg-public-xml-sitemap-generator.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-issslpg-public-randomization.php';
	}

	/**
	 * Maybe Add Schema data.
	 *
	 * @wp-hook wp_head
	 */
	public function maybe_add_schema_data() {
		// Front-Page
		if ( is_front_page() ) {
			$add_organization_schema = ISSSLPG_Options::get_setting( 'add_organization_schema', true, 'iss_schema_settings' );
			if ( $add_organization_schema ) {
				echo ISSSLPG_Public_Schema_Data::get_organization_schema();
			}
		}

		// Landing Page
		if ( ISSSLPG_Landing_Page::is_landing_page() ) {
			$add_local_business_schema = ISSSLPG_Options::get_setting( 'add_local_business_schema', true, 'iss_schema_settings' );
			$add_service_schema = ISSSLPG_Options::get_setting( 'add_service_schema', true, 'iss_schema_settings' );
			if ( $add_local_business_schema ) {
				echo ISSSLPG_Public_Schema_Data::get_local_business_schema();
			}
			if ( $add_service_schema ) {
				echo ISSSLPG_Public_Schema_Data::get_service_schema();
			}
		}

		// Page with [iss_faq] shortcode
		$add_faq_schema = ISSSLPG_Options::get_setting( 'add_faq_schema', true, 'iss_schema_settings' );
		if ( $add_faq_schema ) {
			global $post;
			$content = get_the_content( null, false, $post );
			if ( strpos( $content, '[iss_faq' ) !== false ) {
				echo ISSSLPG_Public_Schema_Data::get_faq_schema();
			}
		}
	}

	/**
	 * Landing page theme template.
	 *
	 * Make sure landing pages use the page instead of the single-post template.
	 *
	 * @since    1.4.15
	 * @wp-hook    template_include
	 */
	function landing_page_theme_template( $template ) {
		if ( ISSSLPG_Landing_Page::is_landing_page() ) {
			$page_template_setting = ISSSLPG_Options::get_setting( 'landing_page_template_file', 'single-issslpg-landing-page.php' );
			$page_template = locate_template( array( $page_template_setting, 'page.php' ) );
			if ( '' != $page_template ) {
				return $page_template ;
			}
		}

		return $template;
	}

	/**
	 * Modify the content. This function can be called by using the the_content
	 * hook.
	 *
	 * @since    1.0.0
	 * @param    string    $content    Content of the current post.
	 * @return    string    $content    Modified content of the current post.
	 * @wp-hook    the_content
	 */
	public function modify_content( $content ) {

		$cache_manager = false;
		if ( ISSSLPG_ISSSCR_Functions::has_cache_manager() ) {
			$post_id = get_the_ID();
			$cache_manager = new ISSSCR_Cache_Manager();
			$cache_manager->load_record( $post_id );
		}

		// If Template Page was updated
		if ( ISSSLPG_Landing_Page::is_landing_page() && $cache_manager && $cache_manager->is_cache_enabled() ) {
			$template_page_id = ISSSLPG_Landing_Page_Api::get_template_page_id();
			$template_page_last_updated = get_post_meta( $template_page_id, '_issslpg_last_updated', true );
			if ( $template_page_last_updated ) {
				$cache_record_expiration_timestamp = (int)$cache_manager->get_record_expiration_timestamp();
				$cache_expiration_time = (int)$cache_manager->get_cache_expiration_time();
				if ( ( $cache_record_expiration_timestamp - $cache_expiration_time ) < $template_page_last_updated ) {
					$cache_manager->delete_record();
				}
			}
		}

		if ( ISSSLPG_Landing_Page::is_landing_page() && ( ! ISSSLPG_Helpers::is_content_randomizer_page() || ! ISSSLPG_Helpers::has_auto_content_replacement_enabled() ) ) {
			$content = ISSSLPG_Landing_Page::modify_content( $content );
		}

//		if ( $cache_manager ) {
////			$cache_manager->save_new_record();
////				error_log( "CONTENT --------------" );
////				error_log( "NEW RECORD $post_id" );
////				error_log( print_r( ISSSCR_Cache_Manager::$new_record, true ) );
////				error_log( "MEMORY RECORD" );
////				error_log( print_r( ISSSCR_Cache_Manager::$memory_record, true ) );
////				error_log( "CURRENT RECORD" );
////				error_log( print_r( ISSSCR_Cache_Manager::$current_record, true ) );
//		}

		return $content;
	}



	/**
	 * Remove autop filter, that adds <p> tags to content
	 *
	 * @wp-hook init
	 */
	function remove_autop() {
		if ( ISSSLPG_Landing_Page::is_landing_page() ) {
			remove_filter( 'the_content', 'wpautop' );
		}
	}



	/**
	 * Remove autop filter, that adds <p> tags to content
	 *
	 * @wp-hook init
	 */
	function add_landing_page_blockstyles() {

		if ( ! ISSSLPG_Landing_Page::is_landing_page() ) {
			return null;
		}

		if ( ! ISSSLPG_Options::get_setting( 'support_issslpg_template_block_editor', false, 'issscr_settings' ) ) {
			return null;
		}

		// Recursive function to filter block names
		function getBlockNames($blocks, &$blockNames = []) {
			foreach ($blocks as $block) {
				if (!empty($block['blockName'])) {
					// Extract block name and add to array
					$blockNames[] = str_replace('core/', '', $block['blockName']);
				}

				// Recursively process inner blocks
				if (!empty($block['innerBlocks'])) {
					getBlockNames($block['innerBlocks'], $blockNames);
				}
			}

			// Make sure all array elements are unique
			$blockNames = array_unique($blockNames);
		}

		// Get content
		$template_page_id = get_post_meta( get_the_ID(), '_issslpg_template_page_id', true );
		$template_post = get_post( $template_page_id );
		$content = $template_post->post_content;
		$content = do_shortcode( $content );

		// Get blocks from content
		$blocks = parse_blocks( $content );
		$blockNames = [];
		getBlockNames( $blocks, $blockNames );

		// Enqueue block CSS
		foreach ($blockNames as $blockName) {
			wp_enqueue_style( "wp-block-{$blockName}" );
		}
	}



	/**
	 * Maybe download landing page HTML export file.
	 *
	 * @wp-hook plugins_loaded
	 */
	public function maybe_download_landing_page_html_file() {
		if ( ! isset( $_GET['export_landing_page'] ) ) {
			return;
		}
		if ( empty( $_GET['export_landing_page'] ) ) {
			return;
		}

		global $post;

		$post_slug = $post->post_name;
		$file_name = $post_slug;

		header( "Content-Disposition: attachment; filename={$file_name}.html" );
		header( 'Content-Type: text/html' );

		// Source: https://stackoverflow.com/questions/3592270/php-get-html-source-code-with-curl
		$ch = curl_init( get_permalink() );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
		$content = curl_exec($ch);
		curl_close($ch);
		echo $content;

		exit();

		// echo '<!DOCTYPE html>';
		// echo '<html>';
		// echo     '<head>';
		// echo         '<title>';
		// echo             the_title();
		// echo         '</title>';
		//              wp_head();
		// echo         '<style>';
		// echo             'body { max-width: 1200px; padding: 80px; margin: 0 auto; font-family: sans-serif; }';
		// echo             '.iss-export-body { display: flex; }';
		// echo             '.iss-export-body > *:nth-child(1) { width: 75% !important; }';
		// echo             '.iss-export-body > *:nth-child(2) { width: 25% !important; margin: 0 !important; padding: 0 !important; }';
		// echo             '.iss-export-body__content { padding-right: 25px; }';
		// echo             '.iss-export-body__sidebar { width: 25%; }';
		// echo         '</style>';
		// echo     '</head>';
		// echo     '<body>';
		// echo         '<h1>';
		// echo             the_title();
		// echo         '</h1>';
		// echo         '<div class="iss-export-body">';
		// echo             '<div class="iss-export-body__content">';
		//                      the_content();
		// echo             '</div>';
		// // echo             '<div class="iss-export-body__sidebar">';
		//                      get_sidebar();
		// // echo             '</div>';
		// echo         '</div>';
		// echo     '</body>';
		// echo '</html>';

		////////////////////////////////////////////////////////////////////////
		// global $post;
		// $tmp_post = $post;
		// // The Query
		// $myposts = get_posts( array(
		// 	'p' => (int)$post_id,
		// 	'post_type' => 'issslpg-landing-page'
		// ) );
		// foreach( $myposts as $post ) :
		// 	// var_dump($post);
		// 	setup_postdata($post);
		// 	$content_post = get_post($post->ID);

		// 	$content.= $content_post->post_content;
		// 	$content = apply_filters('the_content', $content);
		// 	$content = str_replace(']]>', ']]&gt;', $content);
		// 	// $content = do_shortcode($content);
		// 	$content = ISSSLPG_Landing_Page::modify_content( $content );
		// endforeach;
		// $post = $tmp_post;

		// echo $content;
		////////////////////////////////////////////////////////////////////////
		// $query = new WP_Query( array(
		// 	// 'p' => (int)$post_id,
		// 	'post_type'      => 'issslpg-landing-page',
		// 	'posts_per_page' => 1,
		// ) );
		// while ( $query->have_posts() ) :
		// 	$query->the_post();
			// the_content();
		// endwhile;
		// wp_reset_postdata();
		////////////////////////////////////////////////////////////////////////

		// exit();
	}

	/**
	 * Save cache record.
	 *
	 * @since    1.22.0
	 * @wp-hook    wp_footer
	 */
	public function save_cache_record() {
		if ( ! ISSSLPG_ISSSCR_Functions::has_cache_manager() ) {
			return;
		}

		$cache_manager = new ISSSCR_Cache_Manager();
		$cache_manager->load_record();

//		error_log( "NEW RECORD $post_id" );
//		error_log( print_r( ISSSCR_Cache_Manager::$new_record, true ) );
//		error_log( "MEMORY RECORD" );
//		error_log( print_r( ISSSCR_Cache_Manager::$memory_record, true ) );
//		error_log( "CURRENT RECORD" );
//		error_log( print_r( ISSSCR_Cache_Manager::$current_record, true ) );

		$cache_manager->save_new_record();
	}

	/**
	 * Modify document title part.
	 *
	 * @since    1.4.7
	 * @wp-hook    document_title_parts
	 */
	public function modify_document_title_part( $title ) {

		if ( ISSSLPG_Landing_Page::is_landing_page() ) {
			$title['title'] = get_post_meta( get_the_ID(), '_issslpg_page_title', true );
		}

		return $title;
	}

	/**
	 * Modify legacy title.
	 *
	 * If a theme still uses the deprecated function 'wp_title', make sure to
	 * also change title.
	 *
	 * @since    1.4.13
	 * @wp-hook    wp_title
	 */
	public function modify_legacy_title( $title ) {

		if ( ISSSLPG_Landing_Page::is_landing_page() ) {
			$title = get_post_meta( get_the_ID(), '_issslpg_page_title', true );
		}

		return $title;
	}

	/**
	 * Register routing methods.
	 *
	 * @since    1.0.0
	 * @wp-hook    init
	 */
	public function register_routing_methods() {
		$routing = new ISSSLPG_Public_Routing();
		$routing->register_rewrite_tags();
		$routing->register_sitemap_rewrite_rules();
		$routing->flush_rewrite_rules();
	}

	/**
	 * Set landing page ID.
	 *
	 * @since    1.0.0
	 * @wp-hook    set_landing_page_id
	 */
	public function set_landing_page_id() {
		return ISSSLPG_Landing_Page::set_landing_page_id();
	}

	/**
	 * Schedule XML Sitemaps Update.
	 *
	 * @since    1.0.0
	 * @access    public
	 * @wp-hook    init
	 */
	public function schedule_xml_sitemaps_update() {
		if ( ! wp_next_scheduled( 'issslpg_schedule_xml_sitemap_update' ) ) {
			wp_schedule_event( time(), 'daily', 'issslpg_schedule_xml_sitemap_update' );
		}
	}

	/**
	 * Create XML Sitemaps.
	 *
	 * @since    1.0.0
	 * @wp-hook    issslpg_schedule_xml_sitemap_update
	 */
	public function create_xml_sitemaps() {
		$xml_sitemap = new ISSSLPG_Public_XML_Sitemap_Generator();
		$xml_sitemap->create_sitemaps();
	}

	/**
	 * Register the shortcodes.
	 *
	 * @since    1.0.0
	 * @access    public
	 * @wp-hook    pre_get_posts
	 */
	public function register_shortcodes() {
		$shortcodes = new ISSSLPG_Public_Shortcodes();
		add_shortcode( 'iss_large_market_content',                  array( $shortcodes, 'large_market_content' ) );
		add_shortcode( 'iss_alternative_large_market_content',      array( $shortcodes, 'alternative_large_market_content' ) );
		add_shortcode( 'iss_local_static_content',                  array( $shortcodes, 'local_static_content' ) );
		add_shortcode( 'iss_local_image',                           array( $shortcodes, 'local_image' ) );
		add_shortcode( 'iss_local_image_slider',                    array( $shortcodes, 'local_image_slider' ) );
		add_shortcode( 'iss_site_name',                             array( $shortcodes, 'site_name' ) );
		add_shortcode( 'iss_site_name_city_state_zip_code',         array( $shortcodes, 'site_name_city_state_zip_code' ) );
		add_shortcode( 'iss_site_name_city_state_abbr_zip_code',    array( $shortcodes, 'site_name_city_state_abbr_zip_code' ) );
//		add_shortcode( 'iss_template_page_title',                   array( $shortcodes, 'template_page_title' ) );
		add_shortcode( 'iss_page_title',                            array( $shortcodes, 'page_title' ) );
		add_shortcode( 'iss_page_title_city_state_zip_code',        array( $shortcodes, 'page_title_city_state_zip_code' ) );
		add_shortcode( 'iss_page_title_city_state_abbr_zip_code',   array( $shortcodes, 'page_title_city_state_abbr_zip_code' ) );
		add_shortcode( 'iss_country',                               array( $shortcodes, 'country' ) );
		add_shortcode( 'iss_state',                                 array( $shortcodes, 'state' ) );
		add_shortcode( 'iss_province',                              array( $shortcodes, 'state' ) );
		add_shortcode( 'iss_territory',                             array( $shortcodes, 'state' ) );
		add_shortcode( 'iss_state_abbr',                            array( $shortcodes, 'state_abbr' ) );
		add_shortcode( 'iss_county',                                array( $shortcodes, 'county' ) );
		add_shortcode( 'iss_counties',                              array( $shortcodes, 'counties' ) );
		add_shortcode( 'iss_city',                                  array( $shortcodes, 'city' ) );
		add_shortcode( 'iss_map',                                   array( $shortcodes, 'map' ) );
		add_shortcode( 'iss_directions_map',                        array( $shortcodes, 'directions_map' ) );
		add_shortcode( 'iss_city_county',                           array( $shortcodes, 'city_county' ) );
		add_shortcode( 'iss_city_state',                            array( $shortcodes, 'city_state' ) );
		add_shortcode( 'iss_city_state_abbr',                       array( $shortcodes, 'city_state_abbr' ) );
		add_shortcode( 'iss_city_state_zip_code',                   array( $shortcodes, 'city_state_zip_code' ) );
		add_shortcode( 'iss_city_state_abbr_zip_code',              array( $shortcodes, 'city_state_abbr_zip_code' ) );
		add_shortcode( 'iss_city_state_zip_code_phone_number',      array( $shortcodes, 'city_state_zip_code_phone_number' ) );
		add_shortcode( 'iss_city_state_abbr_zip_code_phone_number', array( $shortcodes, 'city_state_abbr_zip_code_phone_number' ) );
		add_shortcode( 'iss_cities_in_county',                      array( $shortcodes, 'cities_in_county' ) );
		add_shortcode( 'iss_zip_code',                              array( $shortcodes, 'zip_code' ) );
		add_shortcode( 'iss_postcode',                              array( $shortcodes, 'zip_code' ) );
		add_shortcode( 'iss_postal_code',                           array( $shortcodes, 'zip_code' ) );
		add_shortcode( 'iss_zip_codes',                             array( $shortcodes, 'zip_codes' ) );
		add_shortcode( 'iss_postcodes',                             array( $shortcodes, 'zip_codes' ) );
		add_shortcode( 'iss_postal_codes',                          array( $shortcodes, 'zip_codes' ) );
		add_shortcode( 'iss_random_location_format',                array( $shortcodes, 'random_location_format' ) );
		add_shortcode( 'iss_phone',                                 array( $shortcodes, 'phone' ) );
		add_shortcode( 'iss_phone_number',                          array( $shortcodes, 'phone' ) );
		add_shortcode( 'iss_phone_link',                            array( $shortcodes, 'phone_link' ) );
		add_shortcode( 'iss_phone_number_link',                     array( $shortcodes, 'phone_link' ) );
		add_shortcode( 'iss_faq',                                   array( $shortcodes, 'faq' ) );
		add_shortcode( 'iss_faq_accordion',                         array( $shortcodes, 'faq_accordion' ) );
		add_shortcode( 'iss_cta_button',                            array( $shortcodes, 'cta_button' ) );
		add_shortcode( 'iss_related_landing_pages',                 array( $shortcodes, 'related_landing_pages' ) );
		add_shortcode( 'iss_alt_text_page_title_city_state',        array( $shortcodes, 'alt_text_page_title_city_state' ) );
		add_shortcode( 'iss_alt_text_page_title_city_state_abbr',   array( $shortcodes, 'alt_text_page_title_city_state_abbr' ) );
		add_shortcode( 'iss_alt_text_page_title_city_state_zip_code_county',      array( $shortcodes, 'alt_text_page_title_city_state_zip_code_county' ) );
		add_shortcode( 'iss_alt_text_page_title_city_state_abbr_zip_code_county', array( $shortcodes, 'alt_text_page_title_city_state_abbr_zip_code_county' ) );
		add_shortcode( 'iss_sitemap',                                             array( $shortcodes, 'sitemap' ) );
		add_shortcode( 'iss_geo_id',                                array( $shortcodes, 'demographics_geo_id' ) );
		add_shortcode( 'iss_city_type',                             array( $shortcodes, 'demographics_city_type' ) );
		add_shortcode( 'iss_population',                            array( $shortcodes, 'demographics_population' ) );
		add_shortcode( 'iss_households',                            array( $shortcodes, 'demographics_households' ) );
		add_shortcode( 'iss_median_income',                         array( $shortcodes, 'demographics_median_income' ) );
		add_shortcode( 'iss_land_area',                             array( $shortcodes, 'demographics_land_area' ) );
		add_shortcode( 'iss_water_area',                            array( $shortcodes, 'demographics_water_area' ) );
		add_shortcode( 'iss_latitude',                              array( $shortcodes, 'demographics_latitude' ) );
		add_shortcode( 'iss_longitude',                             array( $shortcodes, 'demographics_longitude' ) );
		add_shortcode( 'iss_climate_data',                          array( $shortcodes, 'demographics_climate_data' ) );
		add_shortcode( 'iss_crime_data',                            array( $shortcodes, 'demographics_crime_data' ) );
		add_shortcode( 'iss_local_office_address',                  array( $shortcodes, 'local_office_address' ) );
		add_shortcode( 'iss_local_office_street',                   array( $shortcodes, 'local_office_street' ) );
		add_shortcode( 'iss_local_office_city',                     array( $shortcodes, 'local_office_city' ) );
		add_shortcode( 'iss_local_office_zip_code',                 array( $shortcodes, 'local_office_zip_code' ) );

		// Register Dynamic Panel Shortcodes
		$dynamic_shortcode_data = ISSSLPG_Public_Shortcode_Helpers::get_dynamic_shortcode_data();
		foreach ( $dynamic_shortcode_data as $shortcode_types ) {
			foreach ( $shortcode_types as $shortcode_tag => $shortcode_data ) {
				add_shortcode( $shortcode_tag, array( $shortcodes, $shortcode_data['callback'] ) );
			}
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 * @wp-hook    admin_enqueue_scripts
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in ISSSLPG_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The ISSSLPG_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/issslpg-public.css',
			array(),
			$this->version,
			'all'
		);

		global $post;
		if ( is_a( $post, 'WP_Post' )
		     && has_shortcode( $post->post_content, 'iss_faq' )
		     || has_shortcode( $post->post_content, 'iss_faq_accordion' )
		) {
			// Add Dashicons for FAQ accordions
			wp_enqueue_style( $this->plugin_name );
			wp_enqueue_style( 'dashicons' );
		}

		if ( ISSSLPG_Landing_Page::is_landing_page() ) {

			wp_enqueue_style( $this->plugin_name );

			// Enqueue Flexslider
			wp_enqueue_style(
				"iss_flexslider",
				plugin_dir_url( __FILE__ ) . 'plugins/flexslider/flexslider.css',
				array(),
				false
			);
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 * @wp-hook    admin_enqueue_scripts
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in ISSSLPG_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The ISSSLPG_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

//		wp_enqueue_script(
//			$this->plugin_name,
//			plugin_dir_url( __FILE__ ) . 'js/issslpg-public.js',
//			array( 'jquery' ),
//			$this->version,
//			false
//		);

		if ( ISSSLPG_Landing_Page::is_landing_page() ) {
			// Enqueue Flexslider
			wp_enqueue_script(
				"iss_flexslider",
				plugin_dir_url( __FILE__ ) . 'plugins/flexslider/jquery.flexslider-min.js',
				array( 'jquery', $this->plugin_name ),
				$this->version,
				false
			);
		}
	}

}
