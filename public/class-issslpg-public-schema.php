<?php



class ISSSLPG_Public_Schema_Data {

	static public function get_faq_schema() {
		$faq_items = ISSSLPG_Options::get_setting( 'iss_faq', false, 'iss_faq_settings' );

		if ( ! $faq_items ) {
			return false;
		}

		$faq_schema_entries = array();
		foreach ( $faq_items as $faq_item ) {
			$faq_schema_entries[]= array(
				'@type' => 'Question',
				'name' => $faq_item['question'],
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text' => $faq_item['answer'],
				),

			);
		}

		$schema_data = array(
			'@context' => 'http://schema.org',
			'@type' => 'FAQPage',
			'mainEntity' => $faq_schema_entries,
		);

		$json_schema = ISSSLPG_Array_Helpers::get_sanitized_json( $schema_data );

		return self::enclose_in_script_tags( $json_schema, 'FAQ Schema' );
	}

	static public function get_organization_schema() {
		$offers = array();
		$template_pages = get_posts( array(
			'post_type'      => 'issslpg-template',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		) );
		foreach ( $template_pages as $template_page ) {
			$offers[]= array(
				'@type' => 'Offer',
				'itemOffered' => array(
					'@type' => 'Service',
					'name' => $template_page->post_title,
				),
			);
		}

		$name = esc_attr( ISSSLPG_Options::get_setting( 'company_name', false, 'iss_company_info_settings' ) );
		$street = esc_attr( ISSSLPG_Options::get_setting( 'company_address_street', false, 'iss_company_info_settings' ) );
		$city = esc_attr( ISSSLPG_Options::get_setting( 'company_address_city', false, 'iss_company_info_settings' ) );
		$zip_code = esc_attr( ISSSLPG_Options::get_setting( 'company_address_zip_code', false, 'iss_company_info_settings' ) );
		$state = esc_attr( ISSSLPG_Options::get_setting( 'company_address_state', false, 'iss_company_info_settings' ) );
		$country = esc_attr( ISSSLPG_Options::get_setting( 'company_address_country', false, 'iss_company_info_settings' ) );

		// Abort, if company name or address is missing
		if ( ! $name || ! $state || ! $street || ! $city || ! $zip_code ) {
			return false;
		}

		$schema_data = array(
			'@context' => 'http://schema.org',
			'@type' => 'LocalBusiness',
			'url' => site_url(),
			'name' => $name,
//			'legalName' => esc_attr( ISSSLPG_Options::get_setting( 'company_name', false, 'iss_company_info_settings' ) ),
			'description' => esc_attr( ISSSLPG_Options::get_setting( 'company_description', false, 'iss_company_info_settings' ) ),
			'telephone' => esc_attr( ISSSLPG_Options::get_setting( 'company_phone', false, 'iss_company_info_settings' ) ),
			'email' => esc_attr( ISSSLPG_Options::get_setting( 'company_email', false, 'iss_company_info_settings' ) ),
			'logo' => esc_url( ISSSLPG_Options::get_setting( 'company_logo', false, 'iss_company_info_settings' ) ),
			'image' => esc_url( ISSSLPG_Options::get_setting( 'company_image', false, 'iss_company_info_settings' ) ),
			'address' => array(
				'@type' => 'PostalAddress',
				'streetAddress' => $street,
				'addressLocality' => $city,
				'addressRegion' => $state,
				'postalCode' => $zip_code,
				'addressCountry' => $country,
			),
			'openingHoursSpecification' => self::get_business_hours_schema_part(),
			'sameAs' => ISSSLPG_String_Helpers::explode_string_by_new_line( esc_attr( ISSSLPG_Options::get_setting( 'company_external_urls', false, 'iss_company_info_settings' ) ) ),
			'hasOfferCatalog' => array(
				'@type' => 'OfferCatalog',
				'name' => 'Services',
				'itemListElement' => $offers,
			),
		);

		$json_schema = ISSSLPG_Array_Helpers::get_sanitized_json( $schema_data );

		return self::enclose_in_script_tags( $json_schema, 'Organization Schema' );
	}

	static public function get_service_schema() {
		$location = new ISSSLPG_Public_Location;
		$template_page_title = ISSSLPG_Landing_Page_Api::get_template_page_title();
		$county_data = $location->get_county_data();

		$state = $location->get_state();
		$country = $county_data->get_country();
		$office_street = esc_attr( $county_data->get_setting( 'office_street' ) );
		$office_city = esc_attr( $county_data->get_setting( 'office_city' ) );
		$office_zip_code = esc_attr( $county_data->get_setting( 'office_zip_code' ) );

		// If no local office address is set, use HQ data
		if ( ! $office_street || ! $office_city || ! $office_zip_code ) {
			$state = esc_attr( ISSSLPG_Options::get_setting( 'company_address_state', false, 'iss_company_info_settings' ) );
			$country = esc_attr( ISSSLPG_Options::get_setting( 'company_address_country', false, 'iss_company_info_settings' ) );
			$office_city = esc_attr( ISSSLPG_Options::get_setting( 'company_address_city', false, 'iss_company_info_settings' ) );
			$office_street = esc_attr( ISSSLPG_Options::get_setting( 'company_address_street', false, 'iss_company_info_settings' ) );
			$office_zip_code = esc_attr( ISSSLPG_Options::get_setting( 'company_address_zip_code', false, 'iss_company_info_settings' ) );
		}

		$schema_data = array(
			'@context' => 'http://schema.org',
			'@type' => 'Service',
			'serviceType' => $template_page_title,
			'provider' => array(
				'@type' => 'LocalBusiness',
				'name' => esc_attr( ISSSLPG_Options::get_setting( 'company_name', false, 'iss_company_info_settings' ) ),
//				'legalName' => esc_attr( ISSSLPG_Options::get_setting( 'company_name', false, 'iss_company_info_settings' ) ),
				'description' => esc_attr( ISSSLPG_Options::get_setting( 'company_description', false, 'iss_company_info_settings' ) ),
				'telephone' => esc_attr( $location->get_inherited_phone() ),
				'email' => esc_attr( ISSSLPG_Options::get_setting( 'company_email', false, 'iss_company_info_settings' ) ),
				'logo' => esc_url( ISSSLPG_Options::get_setting( 'company_logo', false, 'iss_company_info_settings' ) ),
				'image' => esc_url( ISSSLPG_Options::get_setting( 'company_image', false, 'iss_company_info_settings' ) ),
				'address' => array(
					'@type' => 'PostalAddress',
					'streetAddress' => $office_street,
					'addressLocality' => $office_city,
					'addressRegion' => $state,
					'postalCode' => $office_zip_code,
					'addressCountry' => $country,
				),
			),
			'areaServed' => array(
				'@type' => 'City',
				'name' => esc_attr( $location->get_name() ),
			),
			'hasOfferCatalog' => array(
				'@type' => 'OfferCatalog',
				'name' => $template_page_title,
				'itemListElement' => array(
					'@type' => 'Offer',
					'itemOffered' => array(
						'@type' => 'Service',
						'name' => $template_page_title,
					),
				),
			),
		);

		$json_schema = ISSSLPG_Array_Helpers::get_sanitized_json( $schema_data );

		return self::enclose_in_script_tags( $json_schema, 'Service Schema' );
	}

	static public function get_local_business_schema() {
		$location = new ISSSLPG_Public_Location;
		$template_page_title = ISSSLPG_Landing_Page_Api::get_template_page_title();
		$county_id = ISSSLPG_Landing_Page_Api::get_county_id();
		$county_data = new ISSSLPG_County_Data( intval( $county_id ) );

		$state = $location->get_state();
		$country = $county_data->get_country();
		$office_street = esc_attr( $county_data->get_setting( 'office_street' ) );
		$office_city = esc_attr( $county_data->get_setting( 'office_city' ) );
		$office_zip_code = esc_attr( $county_data->get_setting( 'office_zip_code' ) );

		// If no local office address is set, abort
		if ( ! $office_street || ! $office_city || ! $office_zip_code ) {
			return false;
		}

		$schema_data = array(
			'@context' => 'http://schema.org',
			'@type' => 'LocalBusiness',
			'@id' => get_the_permalink(),
			'name' => esc_attr( ISSSLPG_Options::get_setting( 'company_name', false, 'iss_company_info_settings' ) ),
//			'legalName' => esc_attr( ISSSLPG_Options::get_setting( 'company_name', false, 'iss_company_info_settings' ) ),
			'url' => get_site_url(),
			'description' => esc_attr( ISSSLPG_Options::get_setting( 'company_description', false, 'iss_company_info_settings' ) ),
			'telephone' => esc_attr( $location->get_inherited_phone() ),
			'email' => esc_attr( ISSSLPG_Options::get_setting( 'company_email', false, 'iss_company_info_settings' ) ),
			'logo' => esc_url( ISSSLPG_Options::get_setting( 'company_logo', false, 'iss_company_info_settings' ) ),
			'image' => esc_url( ISSSLPG_Options::get_setting( 'company_image', false, 'iss_company_info_settings' ) ),
			'address' => array(
				'@type' => 'PostalAddress',
				'streetAddress' => $office_street,
				'addressLocality' => $office_city,
				'addressRegion' => $state,
				'postalCode' => $office_zip_code,
				'addressCountry' => $country,
			),
			'areaServed' => array(
				'@type' => 'City',
				'name' => esc_attr( $location->get_name() ),
			),
			'openingHoursSpecification' => self::get_business_hours_schema_part(),
			'sameAs' => ISSSLPG_String_Helpers::explode_string_by_new_line( esc_attr( ISSSLPG_Options::get_setting( 'company_external_urls', false, 'iss_company_info_settings' ) ) ),
			'hasOfferCatalog' => array(
				'@type' => 'OfferCatalog',
				'name' => $template_page_title,
				'itemListElement' => array(
					'@type' => 'Offer',
					'itemOffered' => array(
						'@type' => 'Service',
						'name' => $template_page_title,
					),
				),
			),
		);

		$json_schema = ISSSLPG_Array_Helpers::get_sanitized_json( $schema_data );

		return self::enclose_in_script_tags( $json_schema, 'LocalBusiness Schema' );
	}

	static private function get_business_hours_schema_part() {
		$days = array(
			'sunday' => 'Sunday',
			'monday' => 'Monday',
			'tuesday' => 'Tuesday',
			'wednesday' => 'Wednesday',
			'thursday' => 'Thursday',
			'friday' => 'Friday',
			'saturday' => 'Saturday',
		);

		$output = array();

		foreach ( $days as $day_id => $day_name ) {
			$hours = ISSSLPG_Options::get_setting( "company_hours_{$day_id}", false, 'iss_company_info_settings' );
			if ( $hours ) {
				$business_day_schema_part = array(
					'@type' => 'OpeningHoursSpecification',
					'dayOfWeek' => $day_name,
				);
				if ( isset( $hours['open'] ) ) {
					$business_day_schema_part['opens'] = $hours['open'];
				}
				if ( isset( $hours['close'] ) ) {
					$business_day_schema_part['closes'] = $hours['close'];
				}
				if ( ! empty( $business_day_schema_part['opens'] ) && ! empty( $business_day_schema_part['closes'] ) ) {
					$output[]= $business_day_schema_part;
				}
			}
		}

		if ( ! $output ) {
			return false;
		}

		return $output;
	}

	static private function enclose_in_script_tags( $content, $comment = '' ) {
		$output = '';
		if ( $comment ) {
			$output.= "\n<!-- {$comment} -->\n";
		}
		$output.= '<script type="application/ld+json">';
		$output.= $content;
		$output.= '</script>';
		$output.= "\n";

		return $output;
	}

}
