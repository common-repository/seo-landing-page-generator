<?php

use WeDevs\ORM\WP\County as County;

class ISSSLPG_County_Data extends ISSSLPG_Location_Data {

	protected $county_object;

	public function __construct( $county_handler = false ) {
		$this->set_county( $county_handler );
	}

	private function set_county( $county_handler ) {
		$county = false;

		if ( is_object( $county_handler ) ) {
			$county = $county_handler;
		}
		elseif ( is_numeric( $county_handler ) ) {
			$county = County::where( 'id', $county_handler )->first();
		}

		$this->county_object = $county;
	}

	public function update( $atts ) {
		if ( ! is_object( $this->county_object ) ) {
			return 0;
		}

		$atts = $this->filter_location_atts( $atts );

		if ( ISSSLPG_Helpers::is_county_limit_reached()
		     && isset( $atts['active'] ) && $atts['active'] == '1' ) {
			return -1;
		}

		$county_data = $this->county_object->countyData()->updateOrCreate(
			array( 'county_id' => $this->county_object->id ),
			$atts
		);

		if ( $county_data ) {
			$this->propagate_update( $atts );
		}

		return $county_data;
	}

	protected function propagate_update( $atts ) {

		// Update landing pages (e.g. if county phone number changes)
// 		$cities = $this->get_cities_object();
// 		foreach ( $cities as $city ) {
// 			$city_data = new ISSSLPG_City_Data( $city );
// 			if ( $city_data->status ) {
// //				ISSSLPG_Landing_Page::create_or_update_correlating_landing_pages_by_city( $city, $this->county_object );
// 				// ISSSLPG_Admin_Scheduled_Tasks::add_landing_pages_to_activate( $city_data->id, $this->get_id(), $city_data->status );
// 			}
// 		}

		// County Active
		if ( isset( $atts['active'] ) && $atts['active'] === '1' ) {
			$this->propagate_active_location();
			// Create Landing Pages
//			ISSSLPG_Landing_Page::create_or_update_correlating_landing_pages_by_city( $this->city_object );
		}

		// County Inactive
		else {
			// Maybe deactivate county and state
			$this->propagate_inactive_location();
//			ISSSLPG_Landing_Page::delete_correlating_landing_pages_by_city( $this->id );
		}

	}

	public function propagate_active_location() {
		$state_data = $this->get_state_data_object();

		$state_updated = $state_data->update(
			array( 'active' => true )
		);

		return $state_updated;
	}

	public function propagate_inactive_location() {
		// Go through each county in state...
		$active_counties_in_state = false;
		$state_data = $this->get_state_data_object();
		$counties_in_state = $state_data->get_counties_object();
		foreach ( $counties_in_state as $county ) {
			if ( is_object( $county->countyData ) ) {
				// Check if county is active...
				if ( $county->countyData->active === '1' ) {
					// If an active county was found, set flag and break the loop
					$active_counties_in_state = true;
					break;
				}
			}
		}
		// If state has no active county, deactivate it
		if ( ! $active_counties_in_state ) {
			$state_updated = $state_data->update( array( 'active' => false ) );
		}

	}

	public function get_status() {
		if ( ! is_object( $this->county_object ) ) {
			return false;
		}

		$county_data = $this->county_object->countyData;
		if ( $county_data && ! empty( $county_data->active ) ) {
			$status = ( $county_data->active === '1' );
			return $status;
		}

		return false;
	}

	public function get_id() {
		if ( ! is_object( $this->county_object ) ) {
			return false;
		}

		return $this->county_object->id;
	}

	public function get_name() {
		if ( ! is_object( $this->county_object ) ) {
			return false;
		}

		return $this->county_object->name;
	}

	public function get_settings() {
		if ( ! is_object( $this->county_object->countyData ) ) {
			return false;
		}

		return unserialize( $this->county_object->countyData->settings );
	}

	public function get_setting( $id ) {
		if ( ! is_object( $this->county_object ) ) {
			return false;
		}
		$settings = $this->get_settings();
		if ( ! $settings ) {
			return false;
		}
		if ( ! isset( $settings[$id] ) ) {
			return false;
		}

		return $settings[$id];
	}

	public function get_office_google_pid() {
		if ( ! is_object( $this->county_object->countyData ) ) {
			return false;
		}

		return $this->county_object->countyData->office_google_pid;
	}

	public function get_cities_object() {
		if ( ! is_object( $this->county_object ) ) {
			return false;
		}

		return $this->county_object->cities;
	}

	public function get_active_cities( $limit = 5 ) {
		if ( ! is_object( $this->county_object ) ) {
			return false;
		}

		global $wpdb;
		$results = $wpdb->get_results("
			SELECT c.id, c.name
			FROM {$wpdb->prefix}issslpg_cities c
			INNER JOIN {$wpdb->prefix}issslpg_city_county cc ON ( c.id = cc.city_id )
			INNER JOIN {$wpdb->prefix}issslpg_city_data cd ON ( c.id = cd.city_id )
			WHERE cc.county_id = {$this->id}
			AND cd.active = '1';
		");

		$active_cities = array();
		foreach ( $results as $active_city ) {
			$active_cities[$active_city->id] = $active_city->name;
		}

		// $active_cities = array();
		// $cities = $this->county_object->cities;

		// $i = 0;
		// foreach ( $cities as $city ) {
		// 	$city_data = new ISSSLPG_City_Data( $city );
		// 	if ( $city_data->status ) {
		// 		$active_cities[$city_data->id] = $city_data->name;
		// 	}
		// 	$i++;
		// 	if ( $i == $limit ) {
		// 		break;
		// 	}
		// }

		return $active_cities;
	}

	public function get_state_object() {
		if ( ! is_object( $this->county_object ) ) {
			return false;
		}

		return $this->county_object->state;
	}

	public function get_state_data_object() {
		if ( ! is_object( $this->county_object ) ) {
			return false;
		}
		return new ISSSLPG_State_Data( $this->county_object->state );
	}

	public function get_country() {
		if ( ! is_object( $this->county_object ) ) {
			return false;
		}
		return $this->county_object->state->country->name;
	}

	public function get_country_object() {
		if ( ! is_object( $this->county_object ) ) {
			return false;
		}
		return $this->county_object->state->country;
	}

	public function get_state() {
		if ( ! is_object( $this->county_object ) ) {
			return false;
		}
		return $this->county_object->state->name;
	}

	public function get_state_abbr() {
		if ( ! is_object( $this->county_object ) ) {
			return false;
		}
		return $this->county_object->state->postal_code;
	}

	public function get_phone() {
		if ( ! is_object( $this->county_object ) ) {
			return false;
		}

		// If county has assigned phone number
		$county_data = $this->county_object->countyData;
		if ( $county_data && ! empty( $county_data->phone ) ) {
			return esc_attr( $county_data->phone );
		}

		return false;
	}

	public function get_custom_locations() {
		if ( ! is_object( $this->county_object ) ) {
			return false;
		}
		if ( ! is_object( $this->county_object->countyData ) ) {
			return false;
		}

		$custom_locations = unserialize( $this->county_object->countyData->custom_locations );

		if ( empty( $custom_locations ) || ! is_array( $custom_locations ) ) {
			return false;
		}

		return $custom_locations;
	}

	public function get_inherited_phone() {
		$fallback_phone = esc_attr( ISSSLPG_Options::get_setting( 'company_phone', '000-000-0000' ) );
		$fallback_phone = esc_attr( ISSSLPG_Options::get_setting( 'landing_page_default_phone', $fallback_phone ) );

		if ( ! is_object( $this->county_object ) ) {
			return $fallback_phone;
		}

		// If county has assigned phone number
		$county_data = $this->county_object->countyData;
		if ( $county_data && ! empty( $county_data->phone ) ) {
			return esc_attr( $county_data->phone );
		}

//		// If state has assigned phone number
//		$state_data = $this->county_object->state->stateData;
//		if ( $state_data && ! empty( $state_data->phone ) ) {
//			return esc_attr( $state_data->phone );
//		}

		// If there is no location specific phone number, use the default one,
		// from the settings

		return $fallback_phone;
	}

	public function get_population() {
		if ( ! is_object( $this->county_object->demographics ) ) {
			return false;
		}
		return $this->county_object->demographics->population;
	}

	public function get_households() {
		if ( ! is_object( $this->county_object->demographics ) ) {
			return false;
		}
		return $this->county_object->demographics->households;
	}

	public function get_median_income() {
		if ( ! is_object( $this->county_object->demographics ) ) {
			return false;
		}
		return $this->county_object->demographics->median_income;
	}

	public function get_land_area() {
		if ( ! is_object( $this->county_object->demographics ) ) {
			return false;
		}
		return $this->county_object->demographics->land_area;
	}

	public function get_water_area() {
		if ( ! is_object( $this->county_object->demographics ) ) {
			return false;
		}
		return $this->county_object->demographics->water_area;
	}

	public function get_latitude() {
		if ( ! is_object( $this->county_object->demographics ) ) {
			return false;
		}
		return $this->county_object->demographics->latitude;
	}

	public function get_longitude() {
		if ( ! is_object( $this->county_object->demographics ) ) {
			return false;
		}
		return $this->county_object->demographics->longitude;
	}

	public function get_climate_data() {
		if ( ! is_object( $this->county_object->demographics ) ) {
			return false;
		}
		return unserialize( $this->county_object->demographics->climate_data );
	}

	public function get_fbi_data() {
		if ( ! is_object( $this->county_object->demographics ) ) {
			return false;
		}
		return unserialize( $this->county_object->demographics->fbi_data );
	}

}