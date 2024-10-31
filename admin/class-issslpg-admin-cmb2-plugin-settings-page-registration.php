<?php

class ISSSLPG_Admin_CMB2_Plugin_Settings_Page_Registration {

	public function __construct() {
		$this->register_main_settings_page();
		$this->register_company_info_settings_page();
		$this->register_xml_sitemap_settings_page();
		$this->register_html_sitemap_settings_page();
		$this->register_schema_settings_page();
		$this->register_faq_settings_page();
		$this->register_debug_settings_page();
	}

	public function save_negative_switch_value( $value, $field_args, $field ) {
		if ( $value == false || $value == 'off' ) {
			return 'off';
		}

		return $value;
	}

	public function register_main_settings_page() {

		/**
		 * Registers options page menu item and form.
		 */
		$cmb = new_cmb2_box( array(
			'id'           => 'issslpg_main_settings_page',
			'title'        => esc_html__( 'SEO Landing Page Generator Settings', 'issslpg' ),
			'object_types' => array( 'options-page' ),
			'tab_group'    => 'issslpg_settings',
			'tab_title'    => 'Main',

			/*
			 * The following parameters are specific to the options-page box
			 * Several of these parameters are passed along to add_menu_page()/add_submenu_page().
			 */
			'option_key'  => 'issslpg_settings',
			// The option key and admin menu page slug.
			// 'icon_url'        => '', // Menu icon. Only applicable if 'parent_slug' is left empty.
			'menu_title'      => esc_html__( 'Settings', 'issslpg' ), // Falls back to 'title' (above).
			'parent_slug' => 'issslpg_location_settings',
			// 'capability'      => 'manage_options', // Cap required to view options-page.
			// 'position'        => 1, // Menu position. Only applicable if 'parent_slug' is left empty.
			// 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
			// 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
			// 'save_button'     => esc_html__( 'Save Theme Options', 'issslpg' ), // The text for the options-page save button. Defaults to 'Save'.
			// 'disable_settings_errors' => true, // On settings pages (not options-general.php sub-pages), allows disabling.
			// 'message_cb'      => 'yourprefix_options_page_message_callback',
		) );

//		$cmb->add_field( array(
//				'name'    => esc_html__( 'Default Phone Number', 'issslpg' ),
//				'desc'    => esc_html__( 'This phone number will be displayed on landing pages that have no phone number assiged to their location.', 'issslpg' ),
//				'id'      => 'default_phone',
//				'type'    => 'text',
//				'attributes'  => array(
//						'placeholder' => '000-000-0000',
//				),
////			'sanitization_cb' => array( $this, 'propagate_phone_number' ),
//		) );

//		$cmb->add_field( array(
//				'name'    => esc_html__( 'Company Phone Number', 'issslpg' ),
//				'desc'    => esc_html__( 'This phone number acts as a fallback and will be used by phone shortcodes that are not on landing pages.', 'issslpg' ),
//				'id'      => 'company_phone',
//				'type'    => 'text',
//				'attributes'  => array(
//						'placeholder' => '000-000-0000',
//				),
////			'sanitization_cb' => array( $this, 'propagate_phone_number' ),
//		) );
//
//		$cmb->add_field( array(
//				'name'    => esc_html__( 'Default Email Addresss', 'issslpg' ),
//				'desc'    => esc_html__( '', 'issslpg' ),
//				'id'      => 'default_email',
//				'type'    => 'text',
//				'attributes'  => array(
//						'placeholder' => get_bloginfo( 'admin_email' ),
//				),
////			'sanitization_cb' => array( $this, 'propagate_phone_number' ),
//		) );

		$cmb->add_field( array(
				'name'    => 'Landing Pages',
				'desc'    => '',
				'id'      => 'landing_page_settings_title',
				'type'    => 'title',
		) );

		$page_template_options = array( 'single-issslpg-landing-page.php' => 'Default' );
		// Add theme templates to options
		$templates = wp_get_theme()->get_page_templates();
		foreach ( $templates as $template_name => $template_filename ) {
			$page_template_options[$template_name] = $template_filename;
		}

		$cmb->add_field( array(
			'name'             => 'Template',
			'desc'             => 'Select the template you would like to use to display landing pages.',
			'id'               => 'landing_page_template_file',
			'type'             => 'select',
			'show_option_none' => false,
			'options'          => $page_template_options,
		) );

		$cmb->add_field( array(
			'name'             => 'Page Generator Throttle',
			'desc'             => 'Select how fast your landing pages should be generated.',
			'id'               => 'landing_page_throttle',
			'type'             => 'select',
			'show_option_none' => false,
			'options'          => [
				'' => __( 'No Throttle (fastest)', 'issslpg' ),
				'1000'   => __( '1000 per Day', 'issslpg' ),
				'100'   => __( '100 per Day', 'issslpg' ),
				'10'   => __( '10 per Day', 'issslpg' ),
				'1'   => __( '1 per Day', 'issslpg' ),
			],
		) );

		$cmb->add_field( array(
			'name'    => "Page Generator Priority",
			'desc'    => esc_html__( 'Set the order in which landing pages are generated, based on template pages.', 'issslpg' ),
			'id'      => "template_page_priority",
			'type'    => 'template_page_sort_list',
		) );

		$cmb->add_field( array(
			'name'    => esc_html__( 'Default Phone Number', 'issslpg' ),
			'desc'    => esc_html__( 'This phone number will be displayed on landing pages that have no phone number assigned to their location.', 'issslpg' ),
			'id'      => 'landing_page_default_phone',
			'type'    => 'text',
			'attributes'  => array(
				'placeholder' => '000-000-0000',
			),
//			'sanitization_cb' => array( $this, 'propagate_phone_number' ),
		) );

		$cmb->add_field( array(
			'name'    => esc_html__( 'Landing Page URL Slug', 'issslpg' ),
			'desc'    => __( 'The URL slug that landing pages are prefixed with. If the slug is set to <code>lp</code> for example, the URL for a landing page would look like this: <code>https://yourdomain.com/<b>lp</b>/flood-damage-utica-michigan</code>', 'issslpg' ),
			'id'      => 'landing_page_slug',
			'type'    => 'text',
			'default' => 'lp',
		) );

		$cmb->add_field( array(
				'name'    => esc_html__( 'Heading Format', 'issslpg' ),
				'desc'    => __( 'The format to construct the landing page\'s heading.<br>You can use the following placeholders: <code>[title]</code>, <code>[city]</code>, <code>[state]</code>, <code>[state_abbr]</code>, <code>[zip_code]</code>, <code>[county]</code>, <code>[phone]</code>.', 'issslpg' ),
				'id'      => 'landing_page_heading_format',
				'type'    => 'text',
				'default' => '[title] in [city], [state], [zip_code], [phone]',
		) );

		$cmb->add_field( array(
				'name'    => esc_html__( 'Page Title Format', 'issslpg' ),
				'desc'    => __( 'The format to construct the landing page\'s page title.<br>You can use the following placeholders: <code>[title]</code>, <code>[city]</code>, <code>[state]</code>, <code>[state_abbr]</code>, <code>[zip_code]</code>, <code>[county]</code>, <code>[phone]</code>.', 'issslpg' ),
				'id'      => 'landing_page_page_title_format',
				'type'    => 'text',
				'default' => '[title] in [city], [state], [zip_code], [phone]',
		) );

		$cmb->add_field( array(
				'name'    => esc_html__( 'Show Large Market Content Panel', 'issslpg' ),
				'desc'    => esc_html__( 'Activate to show the Large Market Content panel on landing pages.', 'issslpg' ),
				'id'      => 'show_landing_page_large_market_content_panel',
				'type'    => 'switch',
				'default' => 'on',
				'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
		) );

		$cmb->add_field( array(
				'name'    => esc_html__( 'Show Alternative Large Market Content Panel', 'issslpg' ),
				'desc'    => esc_html__( 'Activate to show the Alternative Large Market Content panel on landing pages.', 'issslpg' ),
				'id'      => 'show_landing_page_alternative_large_market_content_panel',
				'type'    => 'switch',
				'default' => 'off',
				'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
		) );

		$cmb->add_field( array(
				'name'    => esc_html__( 'Show Local Static Content Panel', 'issslpg' ),
				'desc'    => esc_html__( 'Activate to show the Local Static Content panel on landing pages.', 'issslpg' ),
				'id'      => 'show_landing_page_local_static_content_panel',
				'type'    => 'switch',
				'default' => 'off',
				'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
		) );

		$cmb->add_field( array(
				'name'    => esc_html__( 'Show Local Images Panel', 'issslpg' ),
				'desc'    => esc_html__( 'Activate to show the Local Images panel on landing pages.', 'issslpg' ),
				'id'      => 'show_landing_page_local_images_content_panel',
				'type'    => 'switch',
				'default' => 'on',
				'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
		) );

		$cmb->add_field( array(
				'name'    => "Content Panels",
				'desc'    => esc_html__( 'Enter one panel name per line to create custom content panels (e.g. "Service").', 'issslpg' ),
				'id'      => "landing_page_content_panels",
				'type'    => 'textarea_small',
		) );

		$cmb->add_field( array(
				'name'    => "Image Panels",
				'desc'    => esc_html__( 'Enter one panel name per line to create custom image panels (e.g. "Service").', 'issslpg' ),
				'id'      => "landing_page_image_panels",
				'type'    => 'textarea_small',
		) );

		$cmb->add_field( array(
				'name'    => "Keyword Panels",
				'desc'    => esc_html__( 'Enter one panel name per line to create custom keyword panels (e.g. "Service").', 'issslpg' ),
				'id'      => "landing_page_keyword_panels",
				'type'    => 'textarea_small',
		) );

		$cmb->add_field( array(
				'name'    => "Phrase Panels",
				'desc'    => esc_html__( 'Enter one panel name per line to create custom phrase panels (e.g. "Service").', 'issslpg' ),
				'id'      => "landing_page_phrase_panels",
				'type'    => 'textarea_small',
		) );
	}

	public function register_company_info_settings_page() {

		$cmb = new_cmb2_box( array(
				'id'           => 'issslpg_company_info_settings_page',
				'title'        => esc_html__( 'SEO Landing Page Generator Settings', 'issslpg' ),
				'menu_title'   => esc_html__( 'Company Info', 'issslpg' ),
				'object_types' => array( 'options-page' ),
				'tab_title'    => 'Company Info',
				'tab_group'    => 'issslpg_settings',
				'option_key'   => 'iss_company_info_settings',
				'parent_slug'  => 'issslpg_location_settings',
		) );

		$cmb->add_field( array(
				'name'    => "Company Info",
				'desc'    => __( '', 'issslpg' ),
				'id'      => 'company_info_title',
				'type'    => 'title',
		) );

		// $cmb->add_field( array(
		// 	'name'             => 'Organization Type',
		// 	'desc'             => '',
		// 	'id'               => 'organization_type',
		// 	'type'             => 'select',
		// 	'show_option_none' => true,
		// 	'default'          => 'Corporation',
		// 	'options'          => array(
		// 		'Airline'                     => __( 'Airline', 'cmb2' ),
		// 		'Consortium'                  => __( 'Consortium', 'cmb2' ),
		// 		'Corporation'                 => __( 'Corporation', 'cmb2' ),
		// 		'GovernmentOrganization'      => __( 'GovernmentOrganization', 'cmb2' ),
		// 	),
		// ) );

		$cmb->add_field( array(
			'name'    => 'Name',
			'desc'    => __( '', 'issslpg' ),
			'id'      => 'company_name',
			'type'    => 'text',
		) );

		$cmb->add_field( array(
			'name'    => 'Description',
			'desc'    => __( '', 'issslpg' ),
			'id'      => 'company_description',
			'type'    => 'textarea_small',
		) );

		$cmb->add_field( array(
			'name'    => 'Phone',
			'desc'    => __( '', 'issslpg' ),
			'id'      => 'company_phone',
			'type'    => 'text',
		) );

		$cmb->add_field( array(
			'name'    => 'Email',
			'desc'    => __( '', 'issslpg' ),
			'id'      => 'company_email',
			'type'    => 'text',
		) );

		$cmb->add_field( array(
			'name'    => "Company Address",
			'desc'    => __( '', 'issslpg' ),
			'id'      => 'company_address_title',
			'type'    => 'title',
		) );

		$cmb->add_field( array(
			'name'    => 'Street',
			'desc'    => __( '', 'issslpg' ),
			'id'      => 'company_address_street',
			'type'    => 'text',
		) );

		$cmb->add_field( array(
			'name'    => 'City',
			'desc'    => __( '', 'issslpg' ),
			'id'      => 'company_address_city',
			'type'    => 'text',
		) );

		$cmb->add_field( array(
			'name'    => 'State',
			'desc'    => __( '', 'issslpg' ),
			'id'      => 'company_address_state',
			'type'    => 'text',
		) );

		$cmb->add_field( array(
			'name'    => 'ZIP Code',
			'desc'    => __( '', 'issslpg' ),
			'id'      => 'company_address_zip_code',
			'type'    => 'text',
		) );

		$cmb->add_field( array(
			'name'    => 'Country',
			'desc'    => __( '', 'issslpg' ),
			'id'      => 'company_address_country',
			'type'    => 'text',
		) );

		$cmb->add_field( array(
				'name'    => "Company Branding",
				'desc'    => __( '', 'issslpg' ),
				'id'      => 'company_branding_title',
				'type'    => 'title',
		) );

		$is_schema_usage_allowed = ISSSLPG_Helpers::is_schema_usage_allowed();
		$schema_class = '';
		$schema_readonly = false;
		if ( ! $is_schema_usage_allowed ) {
			$schema_class = 'issslpg-cmb-disabled-field';
			$schema_readonly = true;
			$note = 'To enable the <b>Company Branding</b> fields, please download the <b><a href="'. admin_url( 'admin.php?page=issslpg_location_settings-addons' ) .'">Schema Add-on</a></b>.';
			if ( ISSSLPG_Helpers::is_white_labeled() ) {
				$note = 'To enable the <b>Company Branding</b> fields, please download the <b>Schema Add-on</b>.';
			}
			$cmb->add_field( array(
				'note'    => $note,
				'id'      => "enable_company_branding_schema_feature_note",
				'type'    => 'notification',
			) );
		}

		$cmb->add_field( array(
			'name'    => 'Company Logo',
			'desc'    => esc_html__( '', 'issslpg' ),
			'id'      => 'company_logo',
			'type'    => 'file',
			'options' => array(
				'url' => false, // Hide the text input for the url
			),
			'text'    => array(
				'add_upload_file_text' => 'Add Logo' // Change upload button text. Default: "Add or Upload File"
			),
			'query_args' => array(
				'type' => array(
					'image/gif',
					'image/jpeg',
					'image/png',
					'image/webp',
					'image/svg+xml',
				),
			),
			'preview_size' => 'medium', // Image size to use when previewing in the admin
			'classes' => $schema_class,
			'attributes' => array(
				'readonly' => $schema_readonly,
			),
		) );

		$cmb->add_field( array(
			'name'    => 'Company Image',
			'desc'    => esc_html__( 'An image displaying your storefront or office space.', 'issslpg' ),
			'id'      => 'company_image',
			'type'    => 'file',
			'options' => array(
				'url' => false, // Hide the text input for the url
			),
			'text'    => array(
				'add_upload_file_text' => 'Add Image' // Change upload button text. Default: "Add or Upload File"
			),
			'query_args' => array(
				'type' => array(
					'image/gif',
					'image/jpeg',
					'image/png',
					'image/webp',
					'image/svg+xml',
				),
			),
			'preview_size' => 'medium', // Image size to use when previewing in the admin
			'classes' => $schema_class,
			'attributes' => array(
				'readonly' => $schema_readonly,
			),
		) );

		$cmb->add_field( array(
			'name'    => "Company Externals",
			'desc'    => __( '', 'issslpg' ),
			'id'      => 'company_externals_title',
			'type'    => 'title',
		) );

		if ( ! $is_schema_usage_allowed ) {
			$note = 'To enable the <b>Company Externals</b> field, please download the <b><a href="'. admin_url( 'admin.php?page=issslpg_location_settings-addons' ) .'">Schema Add-on</a></b>.';
			if ( ISSSLPG_Helpers::is_white_labeled() ) {
				$note = 'To enable the <b>Company Externals</b> field, please download the <b>Schema Add-on</b>.';
			}
			$cmb->add_field( array(
				'note'    => $note,
				'id'      => "enable_company_externals_schema_feature_note",
				'type'    => 'notification',
			) );
		}

		$cmb->add_field( array(
			'name'    => "External Links",
			'desc'    => esc_html__( 'Add links to other business related websites, social media , etc. Add one link per line.', 'issslpg' ),
			'id'      => "company_external_urls",
			'type'    => 'textarea',
			'classes' => $schema_class,
			'attributes' => array(
				'readonly' => $schema_readonly,
			),
		) );

		$cmb->add_field( array(
			'name'    => "Company Business Hours",
			'desc'    => __( 'Enter your business hours in <a href="http://militarytimechart.com/" target="_blank">military time</a> (e.g. 5 PM as 17:00). Leave fields empty for the days the business is closed. If you\'re open for 24 hours, write 00:00 in the open field and 23:59 in the close field.', 'issslpg' ),
			'id'      => 'company_business_hours_title',
			'type'    => 'title',
		) );

		if ( ! $is_schema_usage_allowed ) {
			$note = 'To enable the <b>Company Business Hours</b> fields, please download the <b><a href="'. admin_url( 'admin.php?page=issslpg_location_settings-addons' ) .'">Schema Add-on</a></b>.';
			if ( ISSSLPG_Helpers::is_white_labeled() ) {
				$note = 'To enable the <b>Company Business Hours</b> fields, please download the <b>Schema Add-on</b>.';
			}
			$cmb->add_field( array(
				'note'    => $note,
				'id'      => "enable_company_business_hours_schema_feature_note",
				'type'    => 'notification',
			) );
		}

		$cmb->add_field( array(
			'name'    => "Sunday",
			'desc'    => __( '', 'issslpg' ),
			'id'      => 'company_hours_sunday',
			'type'    => 'business_day_hours',
			'classes' => $schema_class,
			'attributes' => array(
				'readonly' => $schema_readonly,
			),
		) );
		$cmb->add_field( array(
			'name'    => "Monday",
			'desc'    => __( '', 'issslpg' ),
			'id'      => 'company_hours_monday',
			'type'    => 'business_day_hours',
			'classes' => $schema_class,
			'attributes' => array(
				'readonly' => $schema_readonly,
			),
		) );
		$cmb->add_field( array(
			'name'    => "Tuesday",
			'desc'    => __( '', 'issslpg' ),
			'id'      => 'company_hours_tuesday',
			'type'    => 'business_day_hours',
			'classes' => $schema_class,
			'attributes' => array(
				'readonly' => $schema_readonly,
			),
		) );
		$cmb->add_field( array(
			'name'    => "Wednesday",
			'desc'    => __( '', 'issslpg' ),
			'id'      => 'company_hours_wednesday',
			'type'    => 'business_day_hours',
			'classes' => $schema_class,
			'attributes' => array(
				'readonly' => $schema_readonly,
			),
		) );
		$cmb->add_field( array(
			'name'    => "Thursday",
			'desc'    => __( '', 'issslpg' ),
			'id'      => 'company_hours_thursday',
			'type'    => 'business_day_hours',
			'classes' => $schema_class,
			'attributes' => array(
				'readonly' => $schema_readonly,
			),
		) );
		$cmb->add_field( array(
			'name'    => "Friday",
			'desc'    => __( '', 'issslpg' ),
			'id'      => 'company_hours_friday',
			'type'    => 'business_day_hours',
			'classes' => $schema_class,
			'attributes' => array(
				'readonly' => $schema_readonly,
			),
		) );
		$cmb->add_field( array(
			'name'    => "Saturday",
			'desc'    => __( '', 'issslpg' ),
			'id'      => 'company_hours_saturday',
			'type'    => 'business_day_hours',
			'classes' => $schema_class,
			'attributes' => array(
				'readonly' => $schema_readonly,
			),
		) );

	}

	public function register_schema_settings_page() {
		$cmb = new_cmb2_box( array(
			'id'           => 'issslpg_schema_settings_page',
			'title'        => esc_html__( 'SEO Landing Page Generator Settings', 'issslpg' ),
			'menu_title'   => esc_html__( 'Schema', 'issslpg' ),
			'object_types' => array( 'options-page' ),
			'tab_title'    => 'Schema',
			'tab_group'    => 'issslpg_settings',
			'option_key'   => 'iss_schema_settings',
			'parent_slug'  => 'issslpg_location_settings',
		) );

		$schema_class = '';
		$schema_readonly = false;
		if ( ! ISSSLPG_Helpers::is_schema_usage_allowed() ) {
			$schema_class = 'issslpg-cmb-disabled-field';
			$schema_readonly = true;
			$note = 'To enable the <b>Schema</b> features, please download the <b><a href="'. admin_url( 'admin.php?page=issslpg_location_settings-addons' ) .'">Schema Add-on</a></b>.';
			if ( ISSSLPG_Helpers::is_white_labeled() ) {
				$note = 'To enable the <b>Schema</b> features, please download the <b>Schema Add-on</b>.';
			}
			$cmb->add_field( array(
				'note'    => $note,
				'id'      => "enable_schema_feature_note",
				'type'    => 'notification',
			) );
		}

		$cmb->add_field( array(
			'name'    => esc_html__( 'Organization Schema', 'issslpg' ),
			'desc'    => esc_html__( 'Activate to add Organization schema to the homepage of your site.', 'issslpg' ),
			'id'      => 'add_organization_schema',
			'type'    => 'switch',
			'default' => 'on',
			'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
			'classes' => $schema_class,
			'attributes' => array(
				'readonly' => $schema_readonly,
			),
		) );

		$cmb->add_field( array(
			'name'    => esc_html__( 'LocalBusiness Schema', 'issslpg' ),
			'desc'    => esc_html__( 'Activate to add LocalBusiness schema to landing pages, if the data for a local office was added to the correlating county.', 'issslpg' ),
			'id'      => 'add_local_business_schema',
			'type'    => 'switch',
			'default' => 'on',
			'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
			'classes' => $schema_class,
			'attributes' => array(
				'readonly' => $schema_readonly,
			),
		) );

		$cmb->add_field( array(
			'name'    => esc_html__( 'Service Schema', 'issslpg' ),
			'desc'    => esc_html__( 'Activate to add Service schema to landing pages.', 'issslpg' ),
			'id'      => 'add_service_schema',
			'type'    => 'switch',
			'default' => 'on',
			'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
			'classes' => $schema_class,
			'attributes' => array(
				'readonly' => $schema_readonly,
			),
		) );

		$cmb->add_field( array(
			'name'    => esc_html__( 'FAQ Schema', 'issslpg' ),
			'desc'    => esc_html__( 'Activate to add FAQ schema to any page that contains the FAQ shortcode.', 'issslpg' ),
			'id'      => 'add_faq_schema',
			'type'    => 'switch',
			'default' => 'on',
			'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
			'classes' => $schema_class,
			'attributes' => array(
				'readonly' => $schema_readonly,
			),
		) );
	}

	public function register_xml_sitemap_settings_page() {

		$xml_sitemaps = new ISSSLPG_Public_XML_Sitemap_Generator();

		if ( ISSSLPG_Options::get_xml_sitemap_setting( 'regenerate_xml_sitemaps' ) ) {
			$xml_sitemaps->create_sitemaps();
		}

		$cmb = new_cmb2_box( array(
				'id'           => 'issslpg_xml_sitemap_settings_page',
				'title'        => esc_html__( 'SEO Landing Page Generator Settings', 'issslpg' ),
				'menu_title'   => esc_html__( 'XML Sitemap', 'issslpg' ),
				'object_types' => array( 'options-page' ),
				'tab_title'    => 'XML Sitemap',
				'tab_group'    => 'issslpg_settings',
				'option_key'   => 'issslpg_xml_sitemap_settings',
				'parent_slug'  => 'issslpg_location_settings',
		) );

		$sitemap_index_url = $xml_sitemaps->sitemap_url . '/sitemap_index.xml';
		$cmb->add_field( array(
				'name'    => "Activate Sitemaps",
				'desc'    => "Activate to generate XML sitemaps. The index file can be found here within one day of activation:<br><a target='_blank' href='{$sitemap_index_url}'>{$sitemap_index_url}</a>",
				'id'      => 'activate_xml_sitemaps',
				'type'    => 'switch',
				'default' => 'on',
				'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
		) );

		$cmb->add_field( array(
				'name'    => "Regenerate Sitemaps",
				'desc'    => "Activating this button and hitting \"Save Changes\" will regenerate all sitemaps. The button will turn back to \"off\" afterwards.",
				'id'      => 'regenerate_xml_sitemaps',
				'type'    => 'switch',
				'default' => 'off',
				'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
		) );

		$cmb->add_field( array(
				'name'    => "Supported Post Types",
				'desc'    => "Pick the post types you want to include in your sitemap.",
				'id'      => 'xml_sitemaps_supported_post_types_title',
				'type'    => 'title',
		) );

		$post_types = get_post_types( array(
				'public' => true,
		), 'object' );

		foreach ( $post_types as $post_type ) {
			if ( 'attachment' != $post_type->name
			     && 'issslpg-local' != $post_type->name
			     && 'issslpg-template' != $post_type->name
				 && 'issslpg-landing-page' != $post_type->name )
			{
				$cmb->add_field( array(
						'name'    => "Include \"{$post_type->labels->singular_name}\" Post Type",
						'desc'    => "Activate to include the \"{$post_type->labels->singular_name}\" post type in the XML sitemap.",
						'id'      => "xml_sitemaps_include_post_type_{$post_type->name}",
						'type'    => 'switch',
						'default' => 'on',
						'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
				) );
			}
		}

		$cmb->add_field( array(
				'name'    => "Landing Pages",
				'desc'    => "Pick the landing page types you want to include in your sitemap.",
				'id'      => 'xml_sitemaps_landing_pages_title',
				'type'    => 'title',
		) );

		$template_pages = get_posts( array(
				'post_type'      => 'issslpg-template',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
		) );
		foreach ( $template_pages as $template_page ) :
			$template_page_title = $template_page->post_title;
			$template_page_id = $template_page->ID;
			$cmb->add_field( array(
					'name'    => "Include \"{$template_page_title}\" Landing Pages",
					'desc'    => "Activate to include the \"{$template_page_title}\" landing pages in the XML sitemap.",
					'id'      => "xml_sitemaps_include_template_page_{$template_page_id}",
					'type'    => 'switch',
					'default' => 'on',
					'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
			) );
		endforeach;

	}

	public function register_html_sitemap_settings_page() {

		$cmb = new_cmb2_box( array(
				'id'           => 'issslpg_html_sitemap_settings_page',
				'title'        => esc_html__( 'SEO Landing Page Generator Settings', 'issslpg' ),
				'menu_title'   => esc_html__( 'HTML Sitemap', 'issslpg' ),
				'object_types' => array( 'options-page' ),
				'tab_title'    => 'HTML Sitemap',
				'tab_group'    => 'issslpg_settings',
				'option_key'   => 'issslpg_html_sitemap_settings',
				'parent_slug'  => 'issslpg_location_settings',
		) );

		$cmb->add_field( array(
				'name'    => esc_html__( 'Sitemap Slug', 'issslpg' ),
				'desc'    => __( '', 'issslpg' ),
				'id'      => 'html_sitemap_slug',
				'type'    => 'text',
				'default' => 'sitemap',
		) );

		$cmb->add_field( array(
				'name'    => "Show Link to XML Sitemap",
				'desc'    => "Display a link to the XML sitemap in the HTML sitemap.",
				'id'      => "html_sitemap_show_xml_sitemap_link",
				'type'    => 'switch',
				'default' => 'on',
				'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
		) );

		$cmb->add_field( array(
				'name'    => esc_html__( 'Export Sitemap', 'issslpg' ),
				'desc'    => __( 'Download a CSV file of the Sitemap.', 'issslpg' ),
				'id'      => 'html_sitemap_csv_export',
				'type'    => 'sitemap_export',
		) );

		$cmb->add_field( array(
				'name'    => "Landing Pages",
				'desc'    => "Pick the landing page types you want to include in your sitemap.",
				'id'      => 'html_sitemap_landing_pages_title',
				'type'    => 'title',
		) );

		$template_pages = get_posts( array(
				'post_type'      => 'issslpg-template',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
		) );
		foreach ( $template_pages as $template_page ) :
			$template_page_title = $template_page->post_title;
			$template_page_id = $template_page->ID;
			$cmb->add_field( array(
					'name'    => "Include \"{$template_page_title}\" Landing Pages",
					'desc'    => "Activate to include the \"{$template_page_title}\" landing pages in the HTML sitemap.",
					'id'      => "html_sitemap_include_template_page_{$template_page_id}",
					'type'    => 'switch',
					'default' => 'on',
					'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
			) );
		endforeach;

	}

	public function register_faq_settings_page() {
		$cmb = new_cmb2_box( array(
			'id'           => 'issslpg_faq_settings_page',
			'title'        => esc_html__( 'SEO Landing Page Generator Settings', 'issslpg' ),
			'menu_title'   => esc_html__( 'FAQ', 'issslpg' ),
			'object_types' => array( 'options-page' ),
			'tab_title'    => __( 'FAQ', 'issslpg' ),
			'tab_group'    => 'issslpg_settings',
			'option_key'   => 'iss_faq_settings',
			'parent_slug'  => 'issslpg_location_settings',
		) );

		$faq_class = '';
		$faq_readonly = false;
		if ( ! ISSSLPG_Helpers::is_faq_usage_allowed() ) {
			$faq_class = 'issslpg-cmb-disabled-field';
			$faq_readonly = true;
			$note = 'To enable the <b>FAQ</b> features, please download the <b><a href="'. admin_url( 'admin.php?page=issslpg_location_settings-addons' ) .'">FAQ Add-on</a></b>.';
			if ( ISSSLPG_Helpers::is_white_labeled() ) {
				$note = 'To enable the <b>FAQ</b> features, please download the <b>FAQ Add-on</b>.';
			}
			$cmb->add_field( array(
				'id'      => "enable_faq_feature_note",
				'note'    => $note,
				'type'    => 'notification',
			) );
		}

		$group_id = $cmb->add_field( array(
			'id'          => 'iss_faq',
			'type'        => 'group',
			'desc'        => __( 'You can display the FAQ with the shortcode <code>[iss_faq]</code> or <code>[iss_faq_accordion]</code>, to get an accordion menu.', 'issslpg' ),
			'repeatable'  => true,
			'options'     => array(
				'group_title'   => 'FAQ Item {#}',
				'add_button'    => 'Add FAQ Item',
				'remove_button' => 'Remove FAQ Item',
				'closed'        => true,  // Repeater fields closed by default - neat & compact.
				'sortable'      => true,  // Allow changing the order of repeated groups.
			),
			'classes' => $faq_class,
			'attributes' => array(
				'readonly' => $faq_readonly,
			),
		) );

		$cmb->add_group_field( $group_id, array(
			'name' => 'Question',
			'desc' => __( '', 'issslpg' ),
			'id'   => 'question',
			'type' => 'textarea_small',
		) );

		$cmb->add_group_field( $group_id, array(
			'name' => 'Answer',
			'desc' => __( '', 'issslpg' ),
			'id'   => 'answer',
			'type' => 'textarea',
		) );
	}

	public function register_debug_settings_page() {
		global $wpdb;
		$admin_page_url = admin_url( 'admin.php?page=iss_debug_settings' );

		if ( isset( $_GET['empty_landing_page_queue'] ) && $_GET['empty_landing_page_queue'] == 'true' ) {
			$wpdb->query( "DELETE FROM {$wpdb->prefix}issslpg_scheduled_landing_page_updates" );
			wp_redirect( $admin_page_url );
			exit;
		}
		if ( isset( $_GET['update_all_landing_pages'] ) && $_GET['update_all_landing_pages'] == 'true' ) {
			ISSSLPG_Landing_Page::update_all_landing_pages();
			wp_redirect( $admin_page_url );
			exit;
		}
		if ( isset( $_GET['empty_download_queue'] ) && $_GET['empty_download_queue'] == 'true' ) {
			$wpdb->query( "DELETE FROM {$wpdb->prefix}issslpg_download_queue" );
			wp_redirect( $admin_page_url );
			exit;
		}
		if ( isset( $_GET['delete_logs'] ) && $_GET['delete_logs'] == 'true' ) {
			$wpdb->query( "DELETE FROM {$wpdb->prefix}issslpg_logs" );
			wp_redirect( $admin_page_url );
			exit;
		}

		$cmb = new_cmb2_box( array(
			'id'           => 'issslpg_debug_settings_page',
			'title'        => esc_html__( 'SEO Landing Page Generator Settings', 'issslpg' ),
			'menu_title'   => esc_html__( 'Debug', 'issslpg' ),
			'object_types' => array( 'options-page' ),
			'option_key'   => 'iss_debug_settings',
//			'tab_title'    => __( 'Debug', 'issslpg' ),
//			'tab_group'    => 'issslpg_settings',
			'parent_slug'  => 'issslpg_location_settings',
		) );
//		$cmb = new_cmb2_box( array(
//			'id'           => 'issslpg_faq_settings_page',
//			'title'        => esc_html__( 'SEO Landing Page Generator Settings', 'issslpg' ),
//			'menu_title'   => esc_html__( 'FAQ', 'issslpg' ),
//			'object_types' => array( 'options-page' ),
//			'option_key'   => 'iss_faq_settings',
//		) );

		$activated_landing_page_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}issslpg_scheduled_landing_page_updates WHERE `active` = 1; " );
		if ( $activated_landing_page_count ) {
			$cmb->add_field( array(
				'name' => 'Landing Page Stats',
				'desc' => __( '', 'issslpg' ),
				'id'   => 'landing_page_stats_title',
				'type' => 'title',
			) );
			$existing_landing_page_count = wp_count_posts( 'issslpg-landing-page' )->publish;
			$cmb->add_field( array(
				'name' => 'Landing Pages in Queue',
				'note' => $activated_landing_page_count,
				'desc' => __( '', 'issslpg' ),
				'id'   => 'active_landing_page_queue_count',
				'type' => 'notification',
			) );
			$cmb->add_field( array(
				'name' => 'Existing Landing Pages',
				'note' => $existing_landing_page_count,
				'desc' => __( '', 'issslpg' ),
				'id'   => 'existing_landing_page_count',
				'type' => 'notification',
			) );
			$cmb->add_field( array(
				'name' => 'Expected Landing Page Total',
				'note' => ( $activated_landing_page_count + $existing_landing_page_count ),
				'desc' => __( '', 'issslpg' ),
				'id'   => 'future_landing_page_count',
				'type' => 'notification',
			) );
		}

		$cmb->add_field( array(
			'name' => 'Landing Page Queue',
			'desc' => __( '', 'issslpg' ),
			'id'   => 'landing_page_queue_title',
			'type' => 'title',
		) );

		$landing_page_queue_item_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}issslpg_scheduled_landing_page_updates" );
		$note = $landing_page_queue_item_count ? "{$landing_page_queue_item_count} items in queue" : 'No items in queue';
		$cmb->add_field( array(
			'name' => 'Item Count',
			'note' => $note,
			'desc' => __( '', 'issslpg' ),
			'id'   => 'landing_page_queue_count',
			'type' => 'notification',
		) );

		$cmb->add_field( array(
			'name'         => 'Empty Queue',
			'button_title' => 'Empty Landing Page Queue',
			'href'         => admin_url( 'admin.php?page=iss_debug_settings&empty_landing_page_queue=true' ),
			'desc'         => __( '', 'issslpg' ),
			'id'           => 'landing_page_queue_empty',
			'type'         => 'button',
			'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
		) );

		$cmb->add_field( array(
			'name'         => 'Refill Queue',
			'button_title' => 'Refill Landing Page Queue',
			'href'         => admin_url( 'admin.php?page=iss_debug_settings&update_all_landing_pages=true' ),
			'desc'         => __( '', 'issslpg' ),
			'id'           => 'landing_page_update',
			'type'         => 'button',
			'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
		) );

		$cmb->add_field( array(
			'name' => 'Download Queue',
			'desc' => __( '', 'issslpg' ),
			'id'   => 'download_queue_title',
			'type' => 'title',
		) );

		$cmb->add_field( array(
			'name'         => 'Empty Queue',
			'button_title' => 'Empty Download Queue',
			'href'         => admin_url( 'admin.php?page=iss_debug_settings&empty_download_queue=true' ),
			'desc'         => __( '', 'issslpg' ),
			'id'           => 'download_queue_empty',
			'type'         => 'button',
			'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
		) );

		$cmb->add_field( array(
			'name' => 'Legacy Options',
			'desc' => __( '', 'issslpg' ),
			'id'   => 'legacy_options_title',
			'type' => 'title',
		) );

		$cmb->add_field( array(
			'name'    => esc_html__( 'Use Old Slug Format', 'issslpg' ),
			'desc'    => esc_html__( 'Keep the word "in" in the URL slug of landing pages.', 'issslpg' ),
			'id'      => 'use_old_landing_page_slug_format',
			'type'    => 'switch',
			'default' => 'off',
			'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
		) );

		$cmb->add_field( array(
			'name' => 'Logger',
			'desc' => __( '', 'issslpg' ),
			'id'   => 'logger_options_title',
			'type' => 'title',
		) );

		$cmb->add_field( array(
			'name'    => esc_html__( 'Activate Logger', 'issslpg' ),
			'desc'    => esc_html__( '', 'issslpg' ),
			'id'      => 'active_logger',
			'type'    => 'switch',
			'default' => 'off',
			'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
		) );

		$cmb->add_field( array(
			'name'    => esc_html__( 'Remove Duplicate Logs', 'issslpg' ),
			'desc'    => esc_html__( '', 'issslpg' ),
			'id'      => 'remove_duplicate_logs',
			'type'    => 'switch',
			'default' => 'on',
			'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
		) );

		$cmb->add_field( array(
			'name'         => 'Delete Logs',
			'button_title' => 'Delete Logs',
			'href'         => admin_url( 'admin.php?page=iss_debug_settings&delete_logs=true' ),
			'desc'         => __( '', 'issslpg' ),
			'id'           => 'delete_logs',
			'type'         => 'button',
			'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
		) );

		ISSSLPG_Options::update_setting( 'logs', '', 'iss_debug_settings' );
		$cmb->add_field( array(
			'name'    => '',
			'desc'    => __( '', 'issslpg' ),
			'id'      => 'logs',
			'default' => ISSSLPG_Logger::get_logs_as_text(),
			'type'    => 'textarea_code',
			'attributes' => [
				'readonly' => 'readonly',
			],
		) );

	}

}