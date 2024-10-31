<?php



class ISSSLPG_Admin_CMB2_Plugin_Meta_Box_Registration {

	public $prefix;

	public function __construct() {
		$this->register_generic_landing_page_box();
		$this->register_shortcode_overview_box();
		$this->register_alt_text_shortcode_overview_box();

		$show_large_market_content_panel             = ISSSLPG_Options::get_setting( 'show_landing_page_large_market_content_panel',             true  );
		$show_alternative_large_market_content_panel = ISSSLPG_Options::get_setting( 'show_landing_page_alternative_large_market_content_panel', false );
		$show_local_static_content_panel             = ISSSLPG_Options::get_setting( 'show_landing_page_local_static_content_panel',             false );
		$show_local_images_content_panel             = ISSSLPG_Options::get_setting( 'show_landing_page_local_images_content_panel',             true  );
		if ( $show_large_market_content_panel             ) $this->register_large_market_content_box();
		if ( $show_alternative_large_market_content_panel ) $this->register_alternative_large_market_content_box();
		if ( $show_local_static_content_panel             ) $this->register_local_static_content_box();
		if ( $show_local_images_content_panel             ) $this->register_local_images_box();

		// Register Dynamic Panels
		$this->register_dynamic_content_panels();
		$this->register_dynamic_image_panels();
		$this->register_dynamic_keyword_panels();
		$this->register_dynamic_phrase_panels();
	}

	// public function register_landing_page_export_box() {

	// 	if ( ! isset( $_GET['post'] ) ) {
	// 		return;
	// 	}
	// 	if ( empty( $_GET['post'] ) ) {
	// 		return;
	// 	}

	// 	$post_id = intval( $_GET['post'] );

	// 	$object_types = array( 'issslpg-landing-page' );
	// 	$context      = 'side';
	// 	$priority     = 'default';

	// 	$cmb = new_cmb2_box( array(
	// 		'id'           => 'issslpg_landing_page_export_panel',
	// 		'title'        => __( 'SEO Landing Page Generator: Export', 'issslpg' ),
	// 		'object_types' => $object_types,
	// 		'context'      => $context,
	// 		'priority'     => $priority,
	// 		'closed'       => false,
	// 	) );

	// 	$cmb->add_field( array(
	// 		'button_title' => 'Export Page as HTML',
	// 		'desc'         => __( 'Click the button to get an HTML file of the landing page\'s content.', 'issslpg' ),
	// 		'id'           => '_issslpg_landing_page_html_export',
	// 		'type'         => 'button',
	// 		'href'         => get_permalink( $post_id ) . '?export_landing_page=' . $post_id,
	// 	) );
	// }

	public function register_generic_landing_page_box() {

		$object_types = array( 'issslpg-landing-page' );
		$context      = 'side';
		$priority     = 'default';

		$cmb = new_cmb2_box( array(
				'id'           => 'issslpg_local_content_selector',
				'title'        => __( 'SEO Landing Page Generator', 'issslpg' ),
				'object_types' => $object_types,
				'context'      => $context,
				'priority'     => $priority,
				'closed'       => false,
		) );

		$local_content_pages_array = array();
		$local_content_pages = get_posts( array(
			'post_type'      => 'issslpg-local',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		) );
		foreach( $local_content_pages as $local_content_page ) :
			$local_content_page_id = $local_content_page->ID;
			$local_content_pages_array[$local_content_page_id] = $local_content_page->post_title;
		endforeach;

		$cmb->add_field( array(
				'name'             => 'Select Local Content',
				'desc'             => "Select a Local Content template to have this landing page display its content instead of its own.",
				'id'               => '_issslpg_local_content_page',
				'type'             => 'select',
				'show_option_none' => true,
				'options'          => $local_content_pages_array
		) );

		if ( ISSSLPG_Helpers::is_local_content_usage_allowed() && isset( $_GET['post'] ) ) {
			$post_id = intval( $_GET['post'] );
			$cmb->add_field( array(
				'name'         => 'Export Landing Page',
				'button_title' => 'Export Page as HTML',
				'desc'         => __( 'Click the button to get an HTML file of the landing page\'s content.', 'issslpg' ),
				'id'           => '_issslpg_landing_page_html_export',
				'type'         => 'button',
				'href'         => get_permalink( $post_id ) . '?export_landing_page=' . $post_id,
			) );
		}
	}

	public function register_shortcode_overview_box() {
		$object_types = array( 'issslpg-template', 'issslpg-landing-page', 'issslpg-local' );
		$context      = 'normal';
		$priority     = 'high';

		$cmb = new_cmb2_box( array(
			'id'           => 'issslpg_shortcode-overview-box',
			'title'        => __( 'SEO Landing Page Generator: Shortcode Overview', 'rvn' ),
			'object_types' => $object_types,
			'context'      => $context,
			'priority'     => $priority,
			'closed'       => true,
		) );

		$cmb->add_field( array(
			'name' => 'Shortcode Attributes',
			'desc' => 'There are 2 attributes you can attach to shortcodes: ' .
						'<br><br> <code>[iss_shortcode prefix="in"]</code> adds text in front of the shortcode output.' .
						'<br><br> <code>[iss_shortcode join=", "]</code> adds text in between data. This only works on shortcodes that output 2 sets of data (e.g. [iss_city_state]).',
			'id'   => '_issslpg_shortcode-attributes-description',
			'type' => 'title',
		) );

		$field_1 = $cmb->add_field( array(
			'name' => '[iss_site_name]',
			'desc' => 'Displays the name of your website.',
			'id'   => '_issslpg_site-name-shortcode-description',
			'type' => 'title',
		) );

		$field_2 = $cmb->add_field( array(
			'name' => '[iss_page_title]',
			'desc' => 'Displays the page title.',
			'id'   => '_issslpg_page-title-shortcode-description',
			'type' => 'title',
		) );

		$field_3 = $cmb->add_field( array(
				'name' => '[iss_city]',
				'desc' => 'Displays the city of a location.',
				'id'   => '_issslpg_city-shortcode-description',
				'type' => 'title',
		) );

		$field_4 = $cmb->add_field( array(
				'name' => '[iss_state]',
				'desc' => 'Displays the state of a location. To abbreviate the state name, use <code>[iss_state_abbr]</code>.',
				'id'   => '_issslpg_state-name-shortcode-description',
				'type' => 'title',
		) );

		$field_5 = $cmb->add_field( array(
				'name' => '[iss_zip_code]',
				'desc' => 'Displays the zip code of a location.',
				'id'   => '_issslpg_zip-code-shortcode-description',
				'type' => 'title',
		) );

		$field_6 = $cmb->add_field( array(
				'name' => '[iss_zip_codes]',
				'desc' => 'Displays all zip codes of a location.',
				'id'   => '_issslpg_zip-codes-shortcode-description',
				'type' => 'title',
		) );

		$field_7 = $cmb->add_field( array(
			'name' => '[iss_county]',
			'desc' => 'Displays the county of a location.',
			'id'   => '_issslpg_county-name-shortcode-description',
			'type' => 'title',
		) );

		$field_8 = $cmb->add_field( array(
				'name' => '[iss_counties]',
				'desc' => 'Displays all counties of a location.',
				'id'   => '_issslpg_county-names-shortcode-description',
				'type' => 'title',
		) );

		$field_9 = $cmb->add_field( array(
			'name' => '[iss_phone_number]',
			'desc' => 'Displays the phone number of a location.',
			'id'   => '_issslpg_phone-number-shortcode-description',
			'type' => 'title',
		) );

		$field_10 = $cmb->add_field( array(
			'name' => '[iss_city_county]',
			'desc' => 'Displays the city and county of a location.',
			'id'   => '_issslpg_city-county-shortcode-description',
			'type' => 'title',
		) );

		$field_11 = $cmb->add_field( array(
			'name' => '[iss_city_state]',
			'desc' => 'Displays the city and state of a location. To abbreviate the state name, use <code>[iss_city_state_abbr]</code>.',
			'id'   => '_issslpg_city-state-shortcode-description',
			'type' => 'title',
		) );

		$field_12 = $cmb->add_field( array(
			'name' => '[iss_city_state_zip_code]',
			'desc' => 'Displays the city, state, and zip code of a location. To abbreviate the state name, use <code>[iss_city_state_abbr_zip_code]</code>.',
			'id'   => '_issslpg_city-state-zip-code-shortcode-description',
			'type' => 'title',
		) );

		$field_13 = $cmb->add_field( array(
			'name' => '[iss_cities_in_county]',
			'desc' => 'Displays all cities inside the county of a location as links.',
			'id'   => '_issslpg_cities-in-county-shortcode-description',
			'type' => 'title',
		) );

		$field_14 = $cmb->add_field( array(
				'name' => '[iss_site_name_city_state_zip_code]',
				'desc' => 'Displays the name of your website together with the city, state, and zip code. To abbreviate the state name, use <code>[iss_site_name_city_state_abbr_zip_code]</code>.',
				'id'   => '_issslpg_site-name-city-state-zip-code-shortcode-description',
				'type' => 'title',
		) );

		$field_15 = $cmb->add_field( array(
				'name' => '[iss_page_title_city_state_zip_code]',
				'desc' => 'Displays the name of the page together with the city, state, and zip code. To abbreviate the state name, use <code>[iss_page_title_city_state_abbr_zip_code]</code>.',
				'id'   => '_issslpg_page-title-city-state-zip-code-shortcode-description',
				'type' => 'title',
		) );

		$field_16 = $cmb->add_field( array(
				'name' => '[iss_city_state_zip_code_phone_number]',
				'desc' => 'Displays the city, state, zip code, and phone number of a location. To abbreviate the state name, use <code>[iss_city_state_abbr_zip_code_phone_number]</code>.',
				'id'   => '_issslpg_city-state-zip-code-phone-number-shortcode-description',
				'type' => 'title',
		) );

		$field_17 = $cmb->add_field( array(
				'name' => '[iss_random_location_format]',
				'desc' => 'Displays the location in a random format.',
				'id'   => '_issslpg_random-loaction-format-shortcode-description',
				'type' => 'title',
		) );

		$field_18 = $cmb->add_field( array(
				'name' => '[iss_static_content]',
				'desc' => 'Displays static content.',
				'id'   => '_issslpg_static-content-shortcode-description',
				'type' => 'title',
		) );

		$field_19 = $cmb->add_field( array(
				'name' => '[iss_local_static_content]',
				'desc' => 'Displays local static content for large market areas if entered on the landing page.',
				'id'   => '_issslpg_local-static-content-shortcode-description',
				'type' => 'title',
		) );

		$field_20 = $cmb->add_field( array(
				'name' => '[iss_large_market_content]',
				'desc' => 'Displays content for large market areas if entered on the landing page.',
				'id'   => '_issslpg_large-market-content-shortcode-description',
				'type' => 'title',
		) );

		$field_21 = $cmb->add_field( array(
				'name' => '[iss_alternative_large_market_content]',
				'desc' => 'Displays alternative content for large market areas if entered on the landing page.',
				'id'   => '_issslpg_alternative-large-market-content-shortcode-description',
				'type' => 'title',
		) );

		$field_22 = $cmb->add_field( array(
			'name' => '[iss_local_image]',
			'desc' => 'Displays a randomly selected local image if entered on the landing page.',
			'id'   => '_issslpg_local-image-shortcode-description',
			'type' => 'title',
		) );

		$field_23 = $cmb->add_field( array(
			'name' => '[iss_local_office_address]',
			'desc' => 'Displays the address of a local office, which you can set per county.',
			'id'   => '_issslpg_local-office-address-description',
			'type' => 'title',
		) );

		$field_24 = $cmb->add_field( array(
			'name' => '[iss_local_office_city]',
			'desc' => 'Displays the city of a local office, which you can set per county.',
			'id'   => '_issslpg_local-office-city-description',
			'type' => 'title',
		) );

		$field_25 = $cmb->add_field( array(
			'name' => '[iss_local_office_street]',
			'desc' => 'Displays the street of a local office, which you can set per county.',
			'id'   => '_issslpg_local-office-street-description',
			'type' => 'title',
		) );

		$field_26 = $cmb->add_field( array(
			'name' => '[iss_local_office_zip_code]',
			'desc' => 'Displays the Zip Code of a local office, which you can set per county.',
			'id'   => '_issslpg_local-office-zip-code-description',
			'type' => 'title',
		) );

		$field_27 = $cmb->add_field( array(
				'name' => '[iss_related_landing_pages]',
				'desc' => 'Displays a list category-related landing pages.',
				'id'   => '_issslpg_related-landing-pages-description',
				'type' => 'title',
		) );

		$field_28 = $cmb->add_field( array(
			'name' => '[iss_sitemap]',
			'desc' => 'Displays an entire HTML sitemap. Best used on it\'s own page.',
			'id'   => '_issslpg_sitemap-shortcode-description',
			'type' => 'title',
		) );

		$field_29 = $cmb->add_field( array(
				'name' => '[iss_map]',
				'desc' => 'Displays a map with the location attributed to a landing page.' .
				          'There are 3 attributes you can attach to shortcodes: ' .
				          '<br><br> <code>[iss_map class="aligncenter"]</code> adds classes to the map HTML output (e.g. to center the map).' .
				          '<br><br> <code>[iss_map width="300px"]</code> changes the width of the map.' .
				          '<br><br> <code>[iss_map height="300px"]</code> changes the height of the map.',
				'id'   => '_issslpg_map-shortcode-description',
				'type' => 'title',
		) );

		// Put fields into columns
		$cmb2Grid = new \Cmb2Grid\Grid\Cmb2Grid( $cmb );
		$row = $cmb2Grid->addRow();
		$row->addColumns( array( $field_1, $field_2 ) );
		$row = $cmb2Grid->addRow();
		$row->addColumns( array( $field_3, $field_4 ) );
		$row = $cmb2Grid->addRow();
		$row->addColumns( array( $field_5, $field_6 ) );
		$row = $cmb2Grid->addRow();
		$row->addColumns( array( $field_7, $field_8 ) );
		$row = $cmb2Grid->addRow();
		$row->addColumns( array( $field_9, $field_10 ) );
		$row = $cmb2Grid->addRow();
		$row->addColumns( array( $field_11, $field_12 ) );
		$row = $cmb2Grid->addRow();
		$row->addColumns( array( $field_13, $field_14 ) );
		$row = $cmb2Grid->addRow();
		$row->addColumns( array( $field_15, $field_16 ) );
		$row = $cmb2Grid->addRow();
		$row->addColumns( array( $field_17, $field_18 ) );
		$row = $cmb2Grid->addRow();
		$row->addColumns( array( $field_19, $field_20 ) );
		$row = $cmb2Grid->addRow();
		$row->addColumns( array( $field_21, $field_22 ) );
		$row = $cmb2Grid->addRow();
		$row->addColumns( array( $field_23, $field_24 ) );
		$row = $cmb2Grid->addRow();
		$row->addColumns( array( $field_25, $field_26 ) );
		$row = $cmb2Grid->addRow();
		$row->addColumns( array( $field_27, $field_28 ) );
		$row = $cmb2Grid->addRow();
		$row->addColumns( array( $field_29 ) );
	}

	public function register_alt_text_shortcode_overview_box() {

		$object_types = array( 'issslpg-template', 'issslpg-landing-page', 'issslpg-local' );
		$context      = 'normal';
		$priority     = 'high';

		$cmb = new_cmb2_box( array(
			'id'           => 'issslpg_alt-text-shortcode-overview-box',
			'title'        => __( 'SEO Landing Page Generator: Alt Text Shortcode Overview', 'rvn' ),
			'object_types' => $object_types,
			'context'      => $context,
			'priority'     => $priority,
			'closed'       => true,
		) );

		$cmb->add_field( array(
			'name' => '',
			'desc' => 'Use the following shortcodes in the <b>Alt Text</b> or <b>Title</b> fields, when adding an image from the Media Library.',
			'id'   => '_issslpg_alt-text-shortcode-attributes-description',
			'type' => 'title',
		) );

		$field_1 = $cmb->add_field( array(
			'name' => '[iss_alt_text_page_title_city_state]',
			'desc' => 'Displays the page title together with City, State and an auto-incrementing number. To abbreviate the state name, use <code>[iss_alt_text_page_title_city_state_abbr]</code>.<br><br>Example: <code>My Service in Utica, Michigan (3)</code>',
			'id'   => '_issslpg_alt-text-page-title-city-state-shortcode-description',
			'type' => 'title',
		) );

		$field_2 = $cmb->add_field( array(
			'name' => '[iss_alt_text_page_title_city_state_zip_code_county]',
			'desc' => 'Displays the page title together with City, State, Zip Code, Country and an auto-incrementing number. To abbreviate the state name, use <code>[iss_alt_text_page_title_city_state_abbr_zip_code_county]</code>.<br><br>Example: <code>My Service in Utica, Michigan, 48315, Macomb County (3)</code>',
			'id'   => '_issslpg_alt-text-page-title-city-state-zip-code-county-shortcode-description',
			'type' => 'title',
		) );

		// Put fields into columns
		$cmb2Grid = new \Cmb2Grid\Grid\Cmb2Grid( $cmb );
		$row = $cmb2Grid->addRow();
		$row->addColumns( array( $field_1, $field_2 ) );
	}

	public function register_large_market_content_box() {
		$object_types = array( 'issslpg-landing-page', 'issslpg-local' );
		$context      = 'normal';
		$priority     = 'high';
		$rows_limit   = ISSSLPG_Helpers::get_repeater_box_rows_limit();

		$cmb = new_cmb2_box( array(
			'id'           => 'issslpg_large_market_content_panel',
			'title'        => __( 'SEO Landing Page Generator: Large Market Content', 'issslpg' ),
			'object_types' => $object_types,
			'context'      => $context,
			'priority'     => $priority,
			'closed'       => true,
			'rows_limit'   => $rows_limit,
		) );

		$group_id = $cmb->add_field( array(
			'id'          => '_issslpg_large_market_content',
			'type'        => 'group',
			'desc'        => 'When using the <code>[iss_large_market_content]</code> shortcode on a template page, a content block will be randomly selected and render on your landing page. You can also use it to display a randomly selected content block anywhere else on the page (e.g. in a Widget).',
			'repeatable'  => true,
			'options'     => array(
				'group_title'   => 'Large Market Content Block {#}',
				'add_button'    => 'Add Another Large Market Content Block',
				'remove_button' => 'Remove Large Market Content Block',
				'closed'        => true,  // Repeater fields closed by default - neat & compact.
				'sortable'      => true,  // Allow changing the order of repeated groups.
			)
		) );

		$cmb->add_group_field( $group_id, array(
				'name' => 'Content',
				'desc' => '',
				'id'   => 'content',
				'type' => 'wysiwyg',
				'options' => array(
					'wpautop'       => true,
					'media_buttons' => true,
					'editor_height' => '450',
				),
		) );

		$cmb->add_field( array(
				'name' => 'Pin Large Market Content Block',
				'desc' => "This field lets you turn off the randomization temporarily, if you want to pin and review a specific content block. Simply enter the number of the block you want to pin and the shortcode will only display the content with this block number. Empty the field to turn randomization back on.",
				'id'   => '_issslpg_pinned_large_market_content_block',
				'type' => 'text',
		) );
	}

	public function register_alternative_large_market_content_box() {
		$object_types = array( 'issslpg-landing-page', 'issslpg-local' );
		$context      = 'normal';
		$priority     = 'high';
		$rows_limit   = ISSSLPG_Helpers::get_repeater_box_rows_limit();

		$cmb = new_cmb2_box( array(
			'id'           => 'issslpg_alternative_large_market_content_panel',
			'title'        => __( 'SEO Landing Page Generator: Alternative Large Market Content', 'issslpg' ),
			'object_types' => $object_types,
			'context'      => $context,
			'priority'     => $priority,
			'closed'       => true,
			'rows_limit'   => $rows_limit,
		) );

		$group_id = $cmb->add_field( array(
			'id'          => '_issslpg_alternative_large_market_content',
			'type'        => 'group',
			'desc'        => 'When using the <code>[iss_alternative_large_market_content]</code> shortcode on a template page, a content block will be randomly selected and render on your landing page. You can also use it to display a randomly selected content block anywhere else on the page (e.g. in a Widget).',
			'repeatable'  => true,
			'options'     => array(
				'group_title'   => 'Alternative Large Market Content Block {#}',
				'add_button'    => 'Add Another Alternative Large Market Content Block',
				'remove_button' => 'Remove Alternative Large Market Content Block',
				'closed'        => true,  // Repeater fields closed by default - neat & compact.
				'sortable'      => true,  // Allow changing the order of repeated groups.
			)
		) );

		$cmb->add_group_field( $group_id, array(
				'name' => 'Content',
				'desc' => '',
				'id'   => 'content',
				'type' => 'wysiwyg',
				'options' => array(
					'wpautop'       => true,
					'media_buttons' => true,
					'editor_height' => '450',
				),
		) );

		$cmb->add_field( array(
				'name' => 'Pin Alternative Large Market Content Block',
				'desc' => "This field lets you turn off the randomization temporarily, if you want to pin and review a specific content block. Simply enter the number of the block you want to pin and the shortcode will only display the content with this block number. Empty the field to turn randomization back on.",
				'id'   => '_issslpg_pinned_alternative_large_market_content_block',
				'type' => 'text',
		) );
	}

	public function register_local_static_content_box() {
		$object_types = array( 'issslpg-landing-page', 'issslpg-local' );
		$context      = 'normal';
		$priority     = 'high';
		$rows_limit   = ISSSLPG_Helpers::get_repeater_box_rows_limit();

//		$this->limited_repeater_metaboxes[] = 'issslpg_local_static_content_panel';
		$cmb = new_cmb2_box( array(
				'id'           => 'issslpg_local_static_content_panel',
				'title'        => __( 'SEO Landing Page Generator: Local Static Content', 'issslpg' ),
				'object_types' => $object_types,
				'context'      => $context,
				'priority'     => $priority,
				'show_on_cb'   => array( $this, 'show_box' ),
				'closed'       => true,
				'rows_limit'   => $rows_limit,
		) );

		$group_id = $cmb->add_field( array(
				'id'          => '_issslpg_local_static_content',
				'type'        => 'group',
				'desc'        => 'Display a local static content block wherever you want by using the shortcode <code>[iss_local_static_content block="1"]</code>. Pick the number of the block by entering it into the <code>block</code> parameter. You can put the shortcode into a Content Block or into a Widget to be displayed in a sidebar.',
				'repeatable'  => true,
				'options'     => array(
						'group_title'   => 'Local Static Content Block {#}',
						'add_button'    => 'Add Another Local Static Content Block',
						'remove_button' => 'Remove Local Static Content Block',
						'closed'        => true,  // Repeater fields closed by default - neat & compact.
						'sortable'      => true,  // Allow changing the order of repeated groups.
				),
				'show_on_cb'   => array( $this, 'show_box' ),
		) );

		$cmb->add_group_field( $group_id, array(
				'name' => 'Local Static Content',
				'desc' => '',
				'id'   => 'content',
				'type' => 'wysiwyg',
				'options' => array(
						'wpautop'       => true,
						'media_buttons' => true,
						'editor_height' => '450',
				),
		) );

	}

	public function register_local_images_box() {

		$object_types = array( 'issslpg-landing-page', 'issslpg-local' );
		$context      = 'normal';
		$priority     = 'high';
		$rows_limit   = ISSSLPG_Helpers::get_repeater_box_rows_limit( 2 );

		$cmb = new_cmb2_box( array(
			'id'           => 'issslpg_general_images_panel',
			'title'        => __( 'SEO Landing Page Generator: Local Images', 'issslpg' ),
			'object_types' => $object_types,
			'context'      => $context,
			'priority'     => $priority,
			'closed'       => true,
			'rows_limit'   => $rows_limit,
		) );

		$desc = 'A randomly selected <b>Local Image</b> will be displayed by using the shortcode <code>[iss_local_image]</code>.'
		        .'<br>You can put the shortcode into a Content Block or into a Widget to be displayed in a sidebar.'
		        .'<br><br>You can use the shortcode <code>[iss_local_image_slider]</code> to display a randomly sorted slideshow of all Local Images. Add the parameter <code>auto</code> to the shortcode to have the slideshow rotate automatically.'
		        .'<br><br>You can use the parameter <code>size="medium|large|full"</code> on both shortcodes to determine size of the image/slider.'
		        ."<br><br>You can use the parameter <code>class</code> to apply any class to the image (e.g. <code>class=\"alignleft|alignright|aligncenter\"</code>)."
		        .'<br><br>Examples:'
		        .'<br><br><code>[iss_local_image size="large"]</code> - displays a randomly selected <b>large</b> image.'
		        .'<br><br><code>[iss_local_image_slider auto]</code> - displays a slider that <b>automatically</b> starts rotating.'
		        .'<br><br><code>[iss_local_image_slider auto size="large"]</code> - displays a slider consisting of <b>large</b> sized images that <b>automatically</b> start rotating.'
		        .'<br><br><code>[iss_local_image_slider size="medium"]</code> - displays a slider consisting of <b>medium</b> sized images that has to be clicked on to rotating.';
		$group_id = $cmb->add_field( array(
				'id'          => '_issslpg_local_images',
				'type'        => 'group',
				'desc'        => $desc,
				'repeatable'  => true,
				'options'     => array(
					'group_title'   => 'Local Image {#}',
					'add_button'    => 'Add Another Local Image',
					'remove_button' => 'Remove Local Image',
					'closed'        => true,  // Repeater fields closed by default - neat & compact.
					'sortable'      => true,  // Allow changing the order of repeated groups.
				),
		) );

		$cmb->add_group_field( $group_id, array(
			'name'    => 'Local Image',
			'desc'    => '',
			'id'      => 'image',
			'type'    => 'file',
			// Optional:
			'options' => array(
				'url' => false, // Hide the text input for the url
			),
			'text'    => array(
				'add_upload_file_text' => 'Add Local Image' // Change upload button text. Default: "Add or Upload File"
			),
			// query_args are passed to wp.media's library query.
			'query_args' => array(
				// Or only allow gif, jpg, or png images
				'type' => array(
					'image/gif',
					'image/jpeg',
					'image/png',
				),
			),
			'preview_size' => 'large', // Image size to use when previewing in the admin.
		) );

		$cmb->add_field( array(
				'name'    => 'No Duplicate Outputs',
				'desc'    => 'Activate this option if you don\'t want to see the same image being displayed twice on the same page.<br>Please make sure not to add more image shortcodes in your content than images in this panel.',
				'id'      => "_issslpg_no_duplicate_local_images",
				'type'    => 'switch',
				'default' => 'off',
		) );
	}

	public function register_dynamic_content_panels() {
		$content_panels = ISSSLPG_Options::get_panels( "landing_page_content_panels" );
		$content_panels = ISSSLPG_Helpers::reduce_array_by_dynamic_panel_limit( $content_panels );
		foreach ( $content_panels as $content_panel ) {
			$title  = $content_panel['title'];
			$handle = $content_panel['handle'];
			$this->register_dynamic_content_panel( $title, $handle );
		}
	}

	public function register_dynamic_image_panels() {
		$image_panels = ISSSLPG_Options::get_panels( "landing_page_image_panels" );
		$image_panels = ISSSLPG_Helpers::reduce_array_by_dynamic_panel_limit( $image_panels );
		foreach ( $image_panels as $images_panel ) {
			$title  = $images_panel['title'];
			$handle = $images_panel['handle'];
			$this->register_dynamic_image_panel( $title, $handle );
		}
	}

	public function register_dynamic_keyword_panels() {
		$keyword_panels = ISSSLPG_Options::get_panels( "landing_page_keyword_panels" );
		$keyword_panels = ISSSLPG_Helpers::reduce_array_by_dynamic_panel_limit( $keyword_panels );
		foreach ( $keyword_panels as $keyword_panel ) {
			$title  = $keyword_panel['title'];
			$handle = $keyword_panel['handle'];
			$this->register_dynamic_keyword_panel( $title, $handle );
		}
	}

	public function register_dynamic_phrase_panels() {
		$phrase_panels = ISSSLPG_Options::get_panels( 'landing_page_phrase_panels' );
		$phrase_panels = ISSSLPG_Helpers::reduce_array_by_dynamic_panel_limit( $phrase_panels );
		foreach ( $phrase_panels as $phrase_panel ) {
			$title  = $phrase_panel['title'];
			$handle = $phrase_panel['handle'];
			$this->register_dynamic_phrase_panel( $title, $handle );
		}
	}

	public function register_dynamic_content_panel( $title, $handle ) {

		$object_types = array( 'issslpg-landing-page', 'issslpg-local' );
		$context      = 'normal';
		$priority     = 'high';
		$rows_limit   = ISSSLPG_Helpers::get_repeater_box_rows_limit();

		$cmb = new_cmb2_box( array(
				'id'           => "issslpg_{$handle}_content_panel",
				'title'        => "SEO Landing Page Generator: {$title} Content",
				'object_types' => $object_types,
				'context'      => $context,
				'priority'     => $priority,
				'show_on_cb'   => array( $this, 'show_box' ),
				'closed'       => true,
				'rows_limit'   => $rows_limit,
		) );

		$group_id = $cmb->add_field( array(
				'id'          => "_issslpg_{$handle}_content",
				'type'        => 'group',
				'desc'        => "A randomly selected {$title} Content Block will be displayed by using the shortcode <code>[iss_lp_{$handle}_content]</code>.<br>You can put the shortcode into a Content Block or into a Widget to be displayed in a sidebar.",
				'repeatable'  => true,
				'options'     => array(
						'group_title'   => "{$title} Content Block {#}",
						'add_button'    => "Add Another {$title} Content Block",
						'remove_button' => "Remove {$title} Content Block",
						'closed'        => true,  // Repeater fields closed by default - neat & compact.
						'sortable'      => true,  // Allow changing the order of repeated groups.
				),
		) );

		$cmb->add_group_field( $group_id, array(
				'name' => "{$title} Content",
				'desc' => '',
				'id'   => 'content',
				'type' => 'wysiwyg',
		) );

		$cmb->add_field( array(
				'name' => "Pin {$title} Content Block",
				'desc' => "This field lets you turn off the randomization temporarily, if you want to pin and review a specific content block. Simply enter the number of the block you want to pin and the shortcode will only display the content with this block number. Empty the field to turn randomization back on.",
				'id'   => "_issslpg_pinned_{$handle}_content_block",
				'type' => 'text',
		) );
	}

	public function register_dynamic_image_panel( $title, $handle ) {

		$object_types = array( 'issslpg-landing-page', 'issslpg-local' );
		$context      = 'normal';
		$priority     = 'high';
		$rows_limit   = ISSSLPG_Helpers::get_repeater_box_rows_limit( 2 );

		$cmb = new_cmb2_box( array(
				'id'           => "issslpg_{$handle}_images_panel",
				'title'        => "SEO Landing Page Generator: {$title} Images",
				'object_types' => $object_types,
				'context'      => $context,
				'priority'     => $priority,
				'show_on_cb'   => array( $this, 'show_box' ),
				'closed'       => true,
				'rows_limit'   => $rows_limit,
		) );

		$desc = "A randomly selected <b>{$title} Image</b> will be displayed by using the shortcode <code>[iss_lp_{$handle}_image]</code>."
		        ."<br>You can put the shortcode into a Content Block or into a Widget to be displayed in a sidebar."
		        ."<br><br>You can use the shortcode <code>[iss_lp_{$handle}_image_slider]</code> to display a randomly sorted slideshow of all {$title} Images. Add the parameter <code>auto</code> to the shortcode to have the slideshow rotate automatically."
		        ."<br><br>You can use the parameter <code>size=\"medium|large|full\"</code> on both shortcodes to determine size of the image/slider."
		        ."<br><br>You can use the parameter <code>class</code> to apply any class to the image (e.g. <code>class=\"alignleft|alignright|aligncenter\"</code>)."
		        ."<br><br>Examples:"
		        ."<br><br><code>[iss_lp_{$handle}_image size=\"large\"]</code> - displays a randomly selected <b>large</b> image."
		        ."<br><br><code>[iss_lp_{$handle}_image_slider auto]</code> - displays a slider that <b>automatically</b> starts rotating."
		        ."<br><br><code>[iss_lp_{$handle}_image_slider auto size=\"large\"]</code> - displays a slider consisting of <b>large</b> sized images that <b>automatically</b> start rotating."
		        ."<br><br><code>[iss_lp_{$handle}_image_slider size=\"medium\"]</code> - displays a slider consisting of <b>medium</b> sized images that has to be clicked on to rotating.";
		$group_id = $cmb->add_field( array(
				'id'          => "_issslpg_{$handle}_images",
				'type'        => 'group',
				'desc'        => $desc,
				'repeatable'  => true,
				'options'     => array(
						'group_title'   => "{$title} Image {#}",
						'add_button'    => "Add Another {$title} Image",
						'remove_button' => "Remove {$title} Image",
						'closed'        => true,  // Repeater fields closed by default - neat & compact.
						'sortable'      => true,  // Allow changing the order of repeated groups.
				),
		) );

		$cmb->add_group_field( $group_id, array(
				'name'    => "{$title} Image",
				'desc'    => '',
				'id'      => 'image',
				'type'    => 'file',
			// Optional:
				'options' => array(
						'url' => false, // Hide the text input for the url
				),
				'text'    => array(
						'add_upload_file_text' => "Add {$title} Image" // Change upload button text. Default: "Add or Upload File"
				),
			// query_args are passed to wp.media's library query.
				'query_args' => array(
					// Or only allow gif, jpg, or png images
					'type' => array(
						'image/gif',
						'image/jpeg',
						'image/png',
					),
				),
				'preview_size' => 'large', // Image size to use when previewing in the admin.
		) );

		$cmb->add_field( array(
				'name'    => 'No Duplicate Outputs',
				'desc'    => 'Activate this option if you don\'t want to see the same image being displayed twice on the same page.<br>Please make sure not to add more image shortcodes in your content than images in this panel.',
				'id'      => "_issslpg_no_duplicate_{$handle}_images",
				'type'    => 'switch',
				'default' => 'off',
		) );
	}

	public function register_dynamic_keyword_panel( $title, $handle ) {

		$object_types = array( 'issslpg-landing-page', 'issslpg-local' );
		$context      = 'normal';
		$priority     = 'high';

		$cmb = new_cmb2_box( array(
				'id'           => "issslpg_{$handle}_keywords_panel",
				'title'        => "SEO Landing Page Generator: {$title} Keywords",
				'object_types' => $object_types,
				'context'      => $context,
				'priority'     => $priority,
				'show_on_cb'   => array( $this, 'show_box' ),
				'closed'       => true,
		) );

		$field_1 = $cmb->add_field( array(
				'name' => "Singular {$title} Keywords",
				'desc' => "Enter one keyword per line.<br>Use shortcode <code>[iss_lp_singular_{$handle}]</code> to display a randomly selected singular keyword.",
				'id'   => "_issslpg_singular_{$handle}_keywords",
				'type' => 'textarea',
		) );

		$field_2 = $cmb->add_field( array(
				'name' => "Plural {$title} Keywords",
				'desc' => "Enter one keyword per line.<br>Use shortcode <code>[iss_lp_plural_{$handle}]</code> to display a randomly selected plural keyword.",
				'id'   => "_issslpg_plural_{$handle}_keywords",
				'type' => 'textarea',
		) );

		$field_3 = $cmb->add_field( array(
				'name' => 'No Duplicate Outputs',
				'desc' => 'Activate this option if you don\'t want to see the same keyword being displayed twice on the same page.<br>Please make sure not to add more keyword shortcodes in your content than keywords in this panel.',
				'id'   => "_issslpg_no_duplicate_{$handle}_keywords",
				'type' => 'switch',
		) );

		// Put fields into columns
		$cmb2Grid = new \Cmb2Grid\Grid\Cmb2Grid( $cmb );
		$row = $cmb2Grid->addRow();
		$row->addColumns( array( $field_1, $field_2 ) );
		$row = $cmb2Grid->addRow();
		$row->addColumns( array( $field_3 ) );
	}

	public function register_dynamic_phrase_panel( $title, $handle ) {

		$object_types = array( 'issslpg-landing-page', 'issslpg-local' );
		$context      = 'normal';
		$priority     = 'high';

		$cmb = new_cmb2_box( array(
				'id'           => "issslpg_{$handle}_phrase_panel",
				'title'        => "SEO Landing Page Generator: {$title} Phrases",
				'object_types' => $object_types,
				'context'      => $context,
				'priority'     => $priority,
				'show_on_cb'   => array( $this, 'show_box' ),
				'closed'       => true,
		) );

		$cmb->add_field( array(
				'name' => "{$title} Phrases",
				'desc' => "Enter one phrase per line.<br>Use shortcode <code>[iss_lp_{$handle}_phrase]</code> to display a randomly selected phrase.",
				'id'   => "_issslpg_{$handle}_phrases",
				'type' => 'textarea',
		) );

		$cmb->add_field( array(
				'name' => 'No Duplicate Outputs',
				'desc' => 'Activate this option if you don\'t want to see the same phrase being displayed twice on the same page.<br>Please make sure not to add more phrase shortcodes in your content than phases in this panel.',
				'id'   => "_issslpg_no_duplicate_{$handle}_phrases",
				'type' => 'switch',
		) );
	}



}