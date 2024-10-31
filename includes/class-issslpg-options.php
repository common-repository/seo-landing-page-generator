<?php

class ISSSLPG_Options {

	static public function get_setting( $id, $default = false, $option_key = 'issslpg_settings' ) {
		$settings = get_option( $option_key, $default );
		if ( isset( $settings[$id] ) ) {
			$setting = $settings[$id];
			$setting = ( $setting === 'on'  ) ? true : $setting; // In case we're dealing with a checkbox or switch
			$setting = ( $setting === 'off' ) ? false : $setting; // In case we're dealing with a checkbox or switch
			return $setting;
		}

		return $default;
	}

	static public function update_setting( $id, $value, $option_key = 'issslpg_settings' ) {
		$settings = get_option( $option_key );
		$settings[$id] = $value;
		update_option( $option_key, $settings );
	}

	static public function get_xml_sitemap_setting( $id, $default = false ) {
		return self::get_setting( $id, $default, 'issslpg_xml_sitemap_settings' );
	}

	static public function get_html_sitemap_setting( $id, $default = false ) {
		return self::get_setting( $id, $default, 'issslpg_html_sitemap_settings' );
	}

	static public function set_xml_sitemap_setting( $id, $value ) {
		$settings = get_option( 'issslpg_xml_sitemap_settings' );
		if ( isset( $settings[$id] ) ) {
			$settings[$id] = $value;
			update_option( 'issslpg_xml_sitemap_settings', $settings );
		}
	}

	static public function get_panels( $id ) {
		$panels_atts = array();
		$panels = self::get_setting( $id );
		$panels = explode( "\n", $panels );
		$i = 0;
		foreach ( $panels as $panel ) {
			$title = trim( $panel );
			if ( ! empty( $title ) ) {
				$handle = sanitize_title( $panel );
				$handle = str_replace( '-', '_', $handle );
				$panels_atts[$i]['title']  = $title;
				$panels_atts[$i]['handle'] = $handle;
				$i++;
			}
		}

		return $panels_atts;
	}

}