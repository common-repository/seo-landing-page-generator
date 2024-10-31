<?php

use WeDevs\ORM\WP\City as City;
use WeDevs\ORM\WP\State as State;
use WeDevs\ORM\WP\Country as Country;

class ISSSLPG_City_Data extends ISSSLPG_Location_Data {

	protected $base_url;
	protected $city_object;

	public function __construct( $city_handler = false ) {
		$this->base_url = home_url( '/' );

		$this->set_city( $city_handler );
	}

	private function set_city( $city_handler ) {

		if ( is_object( $city_handler ) ) {
			$city = $city_handler;
		}
		elseif ( is_numeric( $city_handler ) ) {
			$city = City::where( 'id', $city_handler )->first();
		}
		else {
			$city = $this->get_city_object_from_landing_page();
		}

		$this->city_object = $city;
	}

	private function get_city_object_from_landing_page() {
		$city_id = get_post_meta( get_the_ID(), '_issslpg_city_id', true );
		if ( $city_id ) {
			$city = City::where( 'id', $city_id )->first();
			return $city;
		}

		return false;
	}

//	private function get_city_object_from_url() {
//		global $wp_query;
//
//		$city = false;
//
//		// If ID is set
//		if ( isset( $wp_query->query_vars['city_id'] ) ) {
//			$city = City::where('id', $wp_query->query_vars['city_id'])->first();
//		}
//		// If city and state names are set, but not ID
//		elseif ( isset( $wp_query->query_vars['city'] ) && isset( $wp_query->query_vars['state'] ) ) {
//			$city_query_name  = ISSSLPG_Url_Helpers::title_to_query( $wp_query->query_vars['city'] );
//			$state_query_name = ISSSLPG_Url_Helpers::title_to_query( $wp_query->query_vars['state'] );
//			$state = State::where('name', $state_query_name)->first();
//			if ( $state ) {
//				$city = City::where('name', $city_query_name)->where('state_id', $state->id)->first();
//			}
//		}
//
//		return $city;
//	}

	public function get_status() {
		if ( ! is_object( $this->city_object ) ) {
			return false;
		}

		$city_data = $this->city_object->cityData;
		if ( $city_data && ! empty( $city_data->active ) ) {
			$status = ( $city_data->active === '1' );
			return $status;
		}

		return false;
	}

	public function update( $atts, $county_handler = false ) {
		if ( ! is_object( $this->city_object ) ) {
			return 0;
		}

		$atts = $this->filter_location_atts( $atts );

		if ( $this->is_activation_allowed( $atts, $county_handler ) ) {
			$city_data = $this->city_object->cityData()->updateOrCreate(
				array( 'city_id' => $this->city_object->id ),
				$atts
			);
			if ( isset( $atts['active'] ) && $atts['active'] == '1' ) {
				ISSSLPG_Logger::log( 'Activate city: ' . $this->city_object->name, __METHOD__ );
			} elseif ( isset( $atts['active'] ) && $atts['active'] == '0' ) {
				ISSSLPG_Logger::log( 'Deactivate city: ' . $this->city_object->name, __METHOD__ );
			}

			if ( $city_data ) {
				$this->propagate_update( $atts, $county_handler );
				return 1;
			}
		}

		return -1;
	}

	protected function is_activation_allowed( $atts, $county_handler ) {
		if ( $county_handler && isset( $atts['active'] ) && $atts['active'] == '1' ) {
			$county_data = new ISSSLPG_County_Data( $county_handler );
			if ( $county_data ) {
				if ( ! $county_data->status && ISSSLPG_Helpers::is_county_limit_reached() ) {
					return false;
				}
			}
			elseif ( ISSSLPG_Helpers::is_county_limit_reached() ) {
				return false;
			}
		}

		return true;
	}

	protected function propagate_update( $atts, $county_handler = false ) {

		// City Active
		if ( isset( $atts['active'] ) && $atts['active'] == '1' ) {
			// Activate county and state too
			if( $county_handler ) {
				$this->propagate_active_location( $county_handler );
			}
			// Create Landing Pages
			ISSSLPG_Landing_Page::create_or_update_correlating_landing_pages_by_city( $this->city_object, $county_handler, false );

			return true;
		}

		// City Inactive
		else {
			// Maybe deactivate county and state
			$this->propagate_inactive_location( $this->city_object );
			ISSSLPG_Landing_Page::delete_correlating_landing_pages_by_city( $this->get_id() );

			return true;
		}

	}

	public function propagate_active_location( $county_handle ) {
		$county_data = new ISSSLPG_County_Data( $county_handle, true );
		$county_updated = $county_data->update(
			array( 'active' => true )
		);

		return $county_updated;
	}

	public function propagate_inactive_location( $county_handle ) {
		$counties_in_city = $this->get_counties_object();

		// Go through each county the city is in...
		foreach ( $counties_in_city as $county ) {
			$county_data      = new ISSSLPG_County_Data( $county );
			$cities_in_county = $county_data->get_cities_object();
//			$cities_in_county = $county_data->get_active_cities_object();

			// Go through each city...
			$active_cities_in_county  = false;

			foreach ( $cities_in_county as $city ) {
				if ( is_object( $city->cityData ) ) {
					// Check if city is active
					if ( $city->cityData->active === '1' ) {
						// If an active city found, set flag and break the loop
						$active_cities_in_county = true;
						break;
					}
				}
			}
			// If county has no active city, deactivate it
			if ( ! $active_cities_in_county ) {
				$county_data_updated = $county_data->update(
					array( 'active' => false )
				);
			}

		}

	}

	public function get_id() {
		if ( ! is_object( $this->city_object ) ) {
			return false;
		}

		return $this->city_object->id;
	}

	public function get_name() {
		if ( ! is_object( $this->city_object ) ) {
			return false;
		}

		return $this->city_object->name;
	}

	public function get_country_object() {
		if ( ! is_object( $this->city_object ) ) {
			return false;
		}
		$country_object = $this->city_object->country;
		return new ISSSLPG_Country_Data( $country_object, true );
	}

	public function get_county() {
		if ( ! is_object( $this->city_object ) ) {
			return false;
		}
		return $this->city_object->counties->first()->name;
	}

	public function get_state_object() {
		if ( ! is_object( $this->city_object ) ) {
			return false;
		}
		$state_object = $this->city_object->state;
		return new ISSSLPG_State_Data( $state_object, true );
	}

	public function get_state() {
		if ( ! is_object( $this->city_object ) ) {
			return false;
		}
		return $this->city_object->state->name;
	}

	public function get_state_abbr() {
		if ( ! is_object( $this->city_object ) ) {
			return false;
		}
		return $this->city_object->state->postal_code;
	}

	public function get_country() {
		if ( ! is_object( $this->city_object ) ) {
			return false;
		}
		return $this->city_object->country->name;
	}

	public function get_county_object() {
		if ( ! is_object( $this->city_object ) ) {
			return false;
		}
		return $this->city_object->counties->first();
	}

	public function get_county_data_object() {
		if ( ! is_object( $this->city_object ) ) {
			return false;
		}
		$county_object = $this->city_object->counties->first();
		return new ISSSLPG_County_Data( $county_object, true );
	}

	public function get_counties_object() {
		if ( ! is_object( $this->city_object ) ) {
			return false;
		}

		return $this->city_object->counties;
	}

	public function get_active_counties_object() {
		if ( ! is_object( $this->city_object ) ) {
			return false;
		}

		return $this->city_object->counties->where( 'active', '1' )->get();
	}

	public function get_counties() {
		if ( ! is_object( $this->city_object ) ) {
			return false;
		}

		$counties = $this->city_object->counties->toArray();
		$counties_array = array();
		foreach ( $counties as $county ) {
			$counties_array[]= $county['name'];
		}

		return $counties_array;
	}

	public function get_cities_in_county() {
		if ( ! is_object( $this->city_object ) ) {
			return false;
		}

		$page = get_post();
		$page_slug = $page->post_name;
		$county = $this->city_object->counties->first();
		$cities = $county->cities;
		$state_slug = ISSSLPG_Url_Helpers::title_to_slug( $county->state->name );
		$cities_array = array();
		foreach ( $cities as $city ) {
			$city_slug = ISSSLPG_Url_Helpers::title_to_slug( $city->name );
			$url = "{$this->base_url}{$page_slug}/{$city_slug}/{$state_slug}/{$city->id}";
			$cities_array[]= "<a href='{$url}'>{$city->name}</a>";
		}

		return $cities_array;
	}

	public function get_phone() {
		if ( ! is_object( $this->city_object ) ) {
			return false;
		}

		// If city has assigned phone number
		$city_data = $this->city_object->cityData;
		if ( $city_data && ! empty( $city_data->phone ) ) {
			return esc_attr( $city_data->phone );
		}

		return false;
	}

	public function get_inherited_phone() {
		$fallback_phone = esc_attr( ISSSLPG_Options::get_setting( 'company_phone', '000-000-0000' ) );
		$fallback_phone = esc_attr( ISSSLPG_Options::get_setting( 'landing_page_default_phone', $fallback_phone ) );

		if ( ! is_object( $this->city_object ) ) {
			return $fallback_phone;
		}

		// If city has assigned phone number
		$city_data = $this->city_object->cityData;
		if ( $city_data && ! empty( $city_data->phone ) ) {
			return esc_attr( $city_data->phone );
		}

		// If county has assigned phone number
		$county_data = $this->city_object->counties->first()->countyData;
		if ( $county_data && ! empty( $county_data->phone ) ) {
			return esc_attr( $county_data->phone );
		}

		// If state has assigned phone number
//		$state_data = $this->city_object->state->stateData;
//		if ( $state_data && ! empty( $state_data->phone ) ) {
//			return esc_attr( $state_data->phone );
//		}

		// If there is no location specific phone number, use the default one,
		// from the settings
		return $fallback_phone;
	}

	public function get_zip_code_object() {
		if ( ! is_object( $this->city_object ) ) {
			return false;
		}
		return $this->city_object->zipCodes->first();
	}

	public function get_zip_code() {
		if ( ! is_object( $this->city_object ) ) {
			return false;
		}
		$zip_code = $this->city_object->zipCodes->first()->zip_code;

		// Check for American Zip Code
//		if ( strlen( $zip_code ) <= 5 && is_int( $zip_code ) ) {
		if ( 'United States' == $this->get_country() ) {
			$zip_code = str_pad( $zip_code, 5, '0', STR_PAD_LEFT );
		}

		return $zip_code;
	}

	public function get_zip_codes_object() {
		if ( ! is_object( $this->city_object ) ) {
			return false;
		}

		return $this->city_object->zipCodes;
	}

	public function get_zip_codes() {
		if ( ! is_object( $this->city_object ) ) {
			return false;
		}

		$zip_codes = $this->city_object->zipCodes->toArray();
		$zip_codes_array = array();
		foreach ( $zip_codes as $zip_code ) {
			$zip_code = $zip_code['zip_code'];
			// $zip_code = str_pad($zip_code, 5, '0', STR_PAD_LEFT);
			$zip_codes_array[]= $zip_code;
		}

		return $zip_codes_array;
	}

	public function get_geo_id() {
		if ( ! is_object( $this->city_object->demographics ) ) {
			return false;
		}
		return $this->city_object->demographics->geo_id;
	}

	public function get_city_type() {
		if ( ! is_object( $this->city_object->demographics ) ) {
			return false;
		}
		return $this->city_object->demographics->type;
	}

	public function get_population() {
		if ( ! is_object( $this->city_object->demographics ) ) {
			return false;
		}
		return $this->city_object->demographics->population;
	}

	public function get_median_income() {
		if ( ! is_object( $this->city_object->demographics ) ) {
			return false;
		}
		return $this->city_object->demographics->median_income;
	}

	public function get_households() {
		if ( ! is_object( $this->city_object->demographics ) ) {
			return false;
		}
		return $this->city_object->demographics->households;
	}

	public function get_land_area() {
		if ( ! is_object( $this->city_object->demographics ) ) {
			return false;
		}
		return $this->city_object->demographics->land_area;
	}

	public function get_water_area() {
		if ( ! is_object( $this->city_object->demographics ) ) {
			return false;
		}
		return $this->city_object->demographics->water_area;
	}

	public function get_latitude() {
		if ( ! is_object( $this->city_object->demographics ) ) {
			return false;
		}
		return $this->city_object->demographics->latitude;
	}

	public function get_longitude() {
		if ( ! is_object( $this->city_object->demographics ) ) {
			return false;
		}
		return $this->city_object->demographics->longitude;
	}

	public function get_fbi_data() {
		if ( ! is_object( $this->city_object->demographics ) ) {
			return false;
		}
		return unserialize( $this->city_object->demographics->fbi_data );
	}

	public function get_climate_data() {
		if ( ! is_object( $this->city_object->demographics ) ) {
			return false;
		}
		return unserialize( $this->city_object->demographics->climate_data );
	}

}