<?php

class ISSSLPG_Public_Location extends ISSSLPG_Location_Data {

	private $city_data;

	private $county_data;

	public function __construct() {
		$this->city_data = new ISSSLPG_City_Data();
		$this->county_data = new ISSSLPG_County_Data( get_post_meta( ISSSLPG_Landing_Page::get_landing_page_id( false ), '_issslpg_county_id', true ) );
	}

	public function is_city_page() {
		return empty( get_post_meta( ISSSLPG_Landing_Page::get_landing_page_id( false ), '_issslpg_location_name', true ) );
	}

	public function get_county_data() {
		return new ISSSLPG_County_Data( get_post_meta( ISSSLPG_Landing_Page::get_landing_page_id( false ), '_issslpg_county_id', true ) );
	}

	public function get_name() {
		if ( $this->is_city_page() ) {
			return $this->city_data->name;
		}

		return get_post_meta( ISSSLPG_Landing_Page::get_landing_page_id( false ), '_issslpg_location_name', true );
	}

	public function get_inherited_phone() {
		if ( $this->is_city_page() ) {
			return $this->city_data->inherited_phone;
		}

		$custom_location_phone = $this->get_custom_location_data( $this->name, 'phone' );

		if ( empty( $custom_location_phone ) ) {
			$county_data = $this->get_county_data();
			return $county_data->inherited_phone;
		}

		return $this->get_custom_location_data( $this->name, 'phone' );
	}

	public function get_country() {
		$county_data = $this->get_county_data();
		return $county_data->country;
	}

	public function get_counties() {
		if ( $this->is_city_page() ) {
			return $this->city_data->counties;
		}

		return false;
	}

	public function get_state() {
		$county_data = $this->get_county_data();
		return $county_data->state;
	}

	public function get_state_abbr() {
		$county_data = $this->get_county_data();
		return $county_data->state_abbr;
	}

	public function get_county() {
		$county_data = $this->get_county_data();
		return ( $county_data->name ) ? $county_data->name : $this->city_data->county;
	}

	public function get_zip_code() {
		if ( $this->is_city_page() ) {
			return $this->city_data->zip_code;
		}

		$zip_codes = $this->get_custom_location_data( $this->name, 'zip_codes' );
		if ( isset( $zip_codes[0] ) ) {
			return $zip_codes[0];
		}

		return false;
	}

	public function get_zip_codes() {
		if ( $this->is_city_page() ) {
			return $this->city_data->zip_codes;
		}

		return $this->get_custom_location_data( $this->name, 'zip_codes' );
	}

	public function get_custom_location_data( $location_name, $location_item = false ) {
		$county_data = $this->get_county_data();
		$custom_locations = $county_data->custom_locations;

		foreach ( $custom_locations as $custom_location ) {
			if ( $location_name == $custom_location['name'] ) {
				if ( $location_item ) {
					if ( isset( $custom_location[$location_item] ) ) {
						return $custom_location[$location_item];
					}
				} else {
					return $custom_location;
				}
			}
		}

		return false;
	}

	public function get_climate_data() {
		if ( $this->is_city_page() ) {
			return $this->city_data->get_climate_data();
		}

		return false;
	}

	public function get_crime_data() {
		if ( $this->is_city_page() ) {
			return $this->city_data->get_fbi_data();
		}

		return false;
	}

	public function get_city_type() {
		if ( $this->is_city_page() ) {
			return $this->city_data->get_city_type();
		}

		return false;
	}

	public function get_geo_id() {
		if ( $this->is_city_page() ) {
			return $this->city_data->get_geo_id();
		}

		return false;
	}

	public function get_population() {
		if ( $this->is_city_page() ) {
			return $this->city_data->get_population();
		}

		return false;
	}

	public function get_households() {
		if ( $this->is_city_page() ) {
			return $this->city_data->get_households();
		}

		return false;
	}

	public function get_median_income() {
		if ( $this->is_city_page() ) {
			return $this->city_data->get_median_income();
		}

		return false;
	}

	public function get_land_area() {
		if ( $this->is_city_page() ) {
			return $this->city_data->get_land_area();
		}

		return false;
	}

	public function get_water_area() {
		if ( $this->is_city_page() ) {
			return $this->city_data->get_water_area();
		}

		return false;
	}

	public function get_latitude() {
		if ( $this->is_city_page() ) {
			return $this->city_data->get_latitude();
		}

		return false;
	}

	public function get_longitude() {
		if ( $this->is_city_page() ) {
			return $this->city_data->get_longitude();
		}

		return false;
	}

}