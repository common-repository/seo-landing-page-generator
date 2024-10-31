<?php

use WeDevs\ORM\WP\Country as Country;

class ISSSLPG_Country_Data extends ISSSLPG_Location_Data {

	protected $country_object;

	public function __construct( $country_handler = false ) {
		$this->set_country( $country_handler );
	}

	private function set_country( $country_handler ) {
		$country = false;

		if ( is_object( $country_handler ) ) {
			$country = $country_handler;
		}
		elseif ( is_numeric( $country_handler ) ) {
			$country = Country::where( 'id', $country_handler )->first();
		}

		$this->country_object = $country;
	}

	public function update( $atts ) {
		if ( ! is_object( $this->country_object ) ) {
			return false;
		}

		$atts = $this->filter_location_atts( $atts );

		$country_data = $this->country_object->countryData()->updateOrCreate(
			array( 'country_id' => $this->country_object->id ),
			$atts
		);

		return $country_data;
	}

	public function get_status() {
		if ( ! is_object( $this->country_object ) ) {
			return false;
		}

		$country_data = $this->country_object->countryData;
		if ( $country_data && ! empty( $country_data->active ) ) {
			$status = ( $country_data->active === '1' );
			return $status;
		}

		return false;
	}

	public function get_id() {
		return $this->country_object->id;
	}

	public function get_name() {
		return $this->country_object->name;
	}

	public function get_states_object() {
		if ( ! is_object( $this->country_object ) ) {
			return false;
		}

		return $this->country_object->states;
	}

}