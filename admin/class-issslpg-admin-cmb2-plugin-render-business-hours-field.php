<?php

if ( ! class_exists( 'CMB2_Type_Base' ) ) {
	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/plugins/cmb2/includes/types/CMB2_Type_Base.php';
}

/**
 * Handles 'business_day_hours' custom field type.
 */
class ISSSLPG_Admin_CMB2_Plugin_Render_Business_Day_Hours_Field extends CMB2_Type_Base {

	public static function init() {
		add_filter( 'cmb2_render_class_business_day_hours', array( __CLASS__, 'class_name' ) );
		add_filter( 'cmb2_sanitize_business_day_hours', array( __CLASS__, 'maybe_save_split_values' ), 12, 4 );

		/**
		 * The following snippets are required for allowing the business_day_hours field
		 * to work as a repeatable field, or in a repeatable group.
		 */
		add_filter( 'cmb2_sanitize_business_day_hours', array( __CLASS__, 'sanitize' ), 10, 5 );
		add_filter( 'cmb2_types_esc_business_day_hours', array( __CLASS__, 'escape' ), 10, 4 );
//		add_filter( 'cmb2_override_meta_value', array( __CLASS__, 'get_split_meta_value' ), 12, 4 );
	}

	public static function class_name() { return __CLASS__; }

	/**
	 * Handles outputting the business_day_hours field.
	 */
	public function render() {

		// make sure we assign each part of the value we need.
		$value = wp_parse_args( $this->field->escaped_value(), array(
//			'active' => '',
			'open'   => '',
			'close'  => '',
		) );

		$output = '';

//		$output.= $this->types->input( array(
//			'id'    => $this->_id( '_active' ),
//			'name'  => $this->_name( '[active]' ),
//			'checked' => $value['active'],
//			'type'  => 'checkbox',
//			'class' => 'cmb2-option cmb2-list',
//		) );

		$output.= $this->types->input( array(
			'id'    => $this->_id( '_open' ),
			'name'  => $this->_name( '[open]' ),
			'value' => $value['open'],
			'type'  => 'text',
			'class' => 'cmb2-text-small',
			'placeholder' => '09:00',
		) );

		$output.= ' <span>&ndash;</span> ';

		$output.= $this->types->input( array(
			'id'    => $this->_id( '_close' ),
			'name'  => $this->_name( '[close]' ),
			'value' => $value['close'],
			'type'  => 'text',
			'class' => 'cmb2-text-small',
			'placeholder' => '17:00',
		) );

		return $this->rendered( $output );
	}

	/**
	 * Optionally save the Address values into separate fields
	 */
	public static function maybe_save_split_values( $override_value, $value, $object_id, $field_args ) {
		if ( ! isset( $field_args['split_values'] ) || ! $field_args['split_values'] ) {
			// Don't do the override.
			return $override_value;
		}

		$keys = array( 'open', 'close' );

		foreach ( $keys as $key ) {
			if ( ! empty( $value[ $key ] ) ) {
				update_post_meta( $object_id, $field_args['id'] . 'business_day_hours_' . $key, sanitize_text_field( $value[ $key ] ) );
			}
		}

		remove_filter( 'cmb2_sanitize_business_day_hours', array( __CLASS__, 'sanitize' ), 10, 5 );

		// Tell CMB2 we already did the update.
		return true;
	}

	public static function sanitize( $check, $meta_value, $object_id, $field_args, $sanitize_object ) {

		// if not repeatable, bail out.
		if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
			return $check;
		}

		foreach ( $meta_value as $key => $val ) {
			$meta_value[ $key ] = array_filter( array_map( 'sanitize_text_field', $val ) );
		}

		return array_filter( $meta_value );
	}

	public static function escape( $check, $meta_value, $field_args, $field_object ) {
		// if not repeatable, bail out.
		if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
			return $check;
		}

		foreach ( $meta_value as $key => $val ) {
			$meta_value[ $key ] = array_filter( array_map( 'esc_attr', $val ) );
		}

		return array_filter( $meta_value );
	}

//	public static function get_split_meta_value( $data, $object_id, $field_args, $field ) {
//		if ( 'business_day_hours' !== $field->args['type'] ) {
//			return $data;
//		}
//		if ( ! isset( $field->args['split_values'] ) || ! $field->args['split_values'] ) {
//			// Don't do the override.
//			return $data;
//		}
//
//		$prefix = $field->args['id'] . 'business_day_hours_';
//		// Construct an array to iterate to fetch individual meta values for our override.
//		// Should match the values in the render() method.
//		$metakeys = array(
//			'active',
//			'open',
//			'close',
//		);
//
//		$newdata = array();
//		foreach ( $metakeys as $metakey ) {
//			// Use our prefix to construct the whole meta key from the postmeta table.
//			$newdata[ $metakey ] = get_post_meta( $object_id, $prefix . $metakey, true );
//		}
//
//		return $newdata;
//	}
}