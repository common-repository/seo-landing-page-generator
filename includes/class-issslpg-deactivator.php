<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://intellasoftplugins.com
 * @since      1.0.0
 *
 * @package    ISSSLPG
 * @subpackage ISSSLPG/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    ISSSLPG
 * @subpackage ISSSLPG/includes
 * @author     Ruven Pelka <ruven.pelka@gmail.com>
 */
class ISSSLPG_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		self::flush_rewrite_rules();
		self::drop_us_cities_db_tables();
		self::unschedule_events();
	}

	private static function unschedule_events() {
		// XML Sitemap Updates
		$timestamp = wp_next_scheduled( 'issslpg_schedule_xml_sitemap_update' );
		wp_unschedule_event( $timestamp, 'issslpg_schedule_xml_sitemap_update' );

		// Update Landing Pages
		$timestamp = wp_next_scheduled( 'issslpg_schedule_landing_page_updates' );
		wp_unschedule_event( $timestamp, 'issslpg_schedule_landing_page_updates' );

		// Bulk Update Landing Pages
		$timestamp = wp_next_scheduled( 'issslpg_schedule_landing_page_bulk_updates' );
		wp_unschedule_event( $timestamp, 'issslpg_schedule_landing_page_bulk_updates' );
	}

	private static function flush_rewrite_rules() {
		flush_rewrite_rules();
	}

	private static function drop_us_cities_db_tables() {
		$db_tables = new ISSSLPG_Database_Tables();
		$db_tables->drop_tables();
	}

}
