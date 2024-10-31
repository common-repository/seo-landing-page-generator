<?php

class ISSSLPG_Location_Data {

//	public function __construct( $atts = array() ) {
//		// Apply provided attribute values
//		foreach( $atts as $field => $value ) {
//			$this->$field = $value;
//		}
//	}

	public function __get( $name ) {
		$method_name = "get_{$name}";
		if ( method_exists( $this, $method_name ) ) {
			return $this->$method_name();
		}
		elseif ( property_exists( $this, $name ) ) {
			// Getter/Setter not defined so return property if it exists
			return $this->$name;
		}

		return null;
	}

	public function __set( $name, $value ) {
		if ( method_exists( $this, $name ) ) {
			$this->$name( $value );
		}
		else {
			// Getter/Setter not defined so set as property of object
			$this->$name = $value;
		}
	}

	public function __isset($property)
	{
		return isset($this->$property);
	}

	public function filter_location_atts( $atts ) {

		// Filter 'active' attribute
		if ( isset( $atts['active'] ) ) {
			switch ( $atts['active'] ) {
				case '1':
				case 'on':
				case 1:
				case true:
					$atts['active'] = '1';
					break;
				default:
					$atts['active'] = '0';
			}
		}

		return $atts;
	}

}