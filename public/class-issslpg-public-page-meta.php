<?php

class ISSSLPG_Public_Page_Meta {

	public function filter_title( $title_parts ) {
		global $paged, $page;

//		if ( ISSSLPG_Template_Page::is_template_page() ) {
//			$title_parts['title'] = 'MYTITLE';
//		}

		return $title_parts;
	}

	public function add_location_to_page_title( $title, $id = null ) {

		if ( is_page() ) {
			$title = $title . $this->get_location_suffix();
		}

		return $title;
	}

	public function get_location_suffix() {

		$location_suffix = '';

		$location = new ISSSLPG_City_Data();
		$city     = $location->get_city();
		$county   = $location->get_county();
		$state    = $location->get_state();
		$zip_code = $location->get_zip_code();

		if ( ! empty( $city ) && ! empty( $state ) && ! empty( $zip_code ) ) {
			$location_suffix = " in {$city}, {$state}, {$zip_code}";
		} elseif ( ! empty( $county ) && ! empty( $state ) ) {
			$location_suffix = " in {$county} County, {$state}";
		}

		return $location_suffix;
	}

}