<?php

use WeDevs\ORM\WP\State as State;

class ISSSLPG_State_Data extends ISSSLPG_Location_Data {

	protected $state_object;

	public function __construct( $state_handler = false ) {
		$this->set_state( $state_handler );
	}

	private function set_state( $state_handler ) {
		$state = false;

		if ( is_object( $state_handler ) ) {
			$state = $state_handler;
		}
		elseif ( is_numeric( $state_handler ) ) {
			$state = State::where( 'id', $state_handler )->first();
		}

		$this->state_object = $state;
	}

	public function update( $atts ) {
		if ( ! is_object( $this->state_object ) ) {
			return false;
		}

		$atts = $this->filter_location_atts( $atts );

		$state_data = $this->state_object->stateData()->updateOrCreate(
			array( 'state_id' => $this->state_object->id ),
			$atts
		);

		if ( $state_data ) {
			$this->propagate_update( $atts );
		}

		return $state_data;
	}

	protected function propagate_update( $atts ) {

		// State Active
		if ( isset( $atts['active'] ) && $atts['active'] === '1' ) {
			$this->propagate_active_location();
		}

		// State Inactive
		else {
			// Maybe deactivate state
			$this->propagate_inactive_location();
		}

	}

	public function propagate_active_location() {
		$country_data = $this->get_country_data_object();

		$country_updated = $country_data->update(
			array( 'active' => true )
		);

		return $country_updated;
	}

	public function propagate_inactive_location() {
		// Go through each state in country...
		$active_state_in_country = false;
		$country_data = $this->get_country_data_object();
		$states_in_country = $country_data->get_states_object();
		foreach ( $states_in_country as $state ) {
			if ( is_object( $state->stateData ) ) {
				// Check if state is active...
				if ( $state->stateData->active === '1' ) {
					// If an active state was found, set flag and break the loop
					$active_state_in_country = true;
					break;
				}
			}
		}
		// If state has no active county, deactivate it
		if ( ! $active_state_in_country ) {
			$country_updated = $country_data->update( array( 'active' => false ) );
		}

	}

	public function get_country_object() {
		if ( ! is_object( $this->state_object ) ) {
			return false;
		}

		return $this->state_object->country;
	}

	public function get_country_data_object() {
		if ( ! is_object( $this->state_object ) ) {
			return false;
		}
		return new ISSSLPG_Country_Data( $this->state_object->country );
	}

	public function get_status() {
		if ( ! is_object( $this->state_object ) ) {
			return false;
		}

		$state_data = $this->state_object->stateData;
		if ( $state_data && ! empty( $state_data->active ) ) {
			$status = ( $state_data->active === '1' );
			return $status;
		}

		return false;
	}

	public function get_id() {
		return $this->state_object->id;
	}

	public function get_name() {
		return $this->state_object->name;
	}

	public function get_country() {
		return $this->state_object->country->name;
	}

	public function get_office_google_pid() {
		if ( ! is_object( $this->state_object->stateData ) ) {
			return false;
		}

		return $this->state_object->stateData->office_google_pid;
	}

//	public function get_phone() {
//		if ( ! is_object( $this->state_object ) ) {
//			return false;
//		}
//
//		// If state has assigned phone number
//		$state_data = $this->state_object->stateData;
//		if ( $state_data && ! empty( $state_data->phone ) ) {
//			return $state_data->phone;
//		}
//
//		return false;
//	}

//	public function get_inherited_phone() {
//		if ( ! is_object( $this->state_object ) ) {
//			return false;
//		}
//
//		// If state has assigned phone number
//		$state_data = $this->state_object->stateData;
//		if ( $state_data && ! empty( $state_data->phone ) ) {
//			return $state_data->phone;
//		}
//
//		// If there is no location specific phone number, use the default one,
//		// from the settings
//		return ISSSLPG_Options::get_setting( 'default_phone' );
//	}

	public function get_counties_object() {
		if ( ! is_object( $this->state_object ) ) {
			return false;
		}

		return $this->state_object->counties;
	}

	public function get_population() {
		if ( ! is_object( $this->state_object->demographics ) ) {
			return false;
		}
		return $this->state_object->demographics->population;
	}

	public function get_households() {
		if ( ! is_object( $this->state_object->demographics ) ) {
			return false;
		}
		return $this->state_object->demographics->households;
	}

	public function get_median_income() {
		if ( ! is_object( $this->state_object->demographics ) ) {
			return false;
		}
		return $this->state_object->demographics->median_income;
	}

	public function get_land_area() {
		if ( ! is_object( $this->state_object->demographics ) ) {
			return false;
		}
		return $this->state_object->demographics->land_area;
	}

	public function get_water_area() {
		if ( ! is_object( $this->state_object->demographics ) ) {
			return false;
		}
		return $this->state_object->demographics->water_area;
	}

	public function get_population_data() {
		if ( ! is_object( $this->state_object->demographics ) ) {
			return false;
		}

		$total_population = $this->state_object->demographics->population;
		$population_data = unserialize( $this->state_object->demographics->population_data );
		$population_data['total'] = $total_population;

		return $population_data;
	}

	public function get_education_data() {
		if ( ! is_object( $this->state_object->demographics ) ) {
			return false;
		}

		return unserialize( $this->state_object->demographics->education_data );
	}

	public function get_fbi_data() {
		if ( ! is_object( $this->state_object->demographics ) ) {
			return false;
		}

		return unserialize( $this->state_object->demographics->fbi_data );
	}

}