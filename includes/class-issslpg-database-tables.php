<?php

use WeDevs\ORM\WP\State as State;

class ISSSLPG_Database_Tables {

	private $table_name_prefix;

	private $charset_collate;

	private $data_url;

	public function __construct() {
		global $wpdb;
		$this->table_name_prefix = "{$wpdb->prefix}issslpg_";
		$this->charset_collate   = $wpdb->get_charset_collate();
//		$this->data_url          = 'http://data.intellasoftplugins.com/v1/';
		$this->data_url          = plugin_dir_path( dirname( __FILE__ ) ) . 'includes/sql_data/';
	}

	public function build_tables() {
		$this->build_download_queue_table();
		$this->build_scheduled_landing_page_updates_table();
		$this->build_excluded_county_template_pages_table();
		$this->build_city_data_table();
		$this->build_county_data_table();
		$this->build_state_data_table();
		$this->build_country_data_table();
		$this->build_cities_table();
		$this->build_city_county_table();
		$this->build_city_zip_code_table();
		$this->build_counties_table();
		$this->build_states_table();
		$this->build_zip_codes_table();
		$this->build_countries_table();
		$this->build_state_demographics_table();
		$this->build_county_demographics_table();
		$this->build_city_demographics_table();
		$this->build_logs_table();
	}

	public function drop_tables() {
		global $wpdb;
		$sql = "DROP TABLE IF EXISTS ";
		// $sql.= $this->table_name_prefix . "city_data, ";
		// $sql.= $this->table_name_prefix . "county_data, ";
		// $sql.= $this->table_name_prefix . "state_data, ";

		// $sql.= $this->table_name_prefix . "cities, ";
		// $sql.= $this->table_name_prefix . "city_county, ";
		// $sql.= $this->table_name_prefix . "city_zip_code, ";
		// $sql.= $this->table_name_prefix . "counties, ";
		// $sql.= $this->table_name_prefix . "zip_codes, ";
		$sql.= $this->table_name_prefix . "states, ";
		$sql.= $this->table_name_prefix . "countries; ";

		$wpdb->query( $sql );
	}

	public function build_download_queue_table() {
		$table_name = $this->table_name_prefix . 'download_queue';
		$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
				`id` int(10) AUTO_INCREMENT PRIMARY KEY,
				`unit_id` int(10) UNSIGNED NOT NULL,
				`unit_category` varchar(255) NOT NULL,
				`table_name` varchar(255) NOT NULL,
				`item_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
				`total_count` int(10) UNSIGNED NOT NULL
				) {$this->charset_collate}; ";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public function seed_download_queue_with_us_location_data() {
		global $wpdb;

		$table_names = array(
			'counties',
			'cities',
			'city_county',
			'city_zip_code',
			'zip_codes',
		);

		for ( $i = 1; $i <= 51; $i++ ) {
			foreach ( $table_names as $table_name ) {
				$wpdb->replace(
					"{$wpdb->prefix}issslpg_download_queue",
					array(
						'unit_id'       => $i,
						'unit_category' => 'locations',
						'table_name'    => $table_name,
						'item_count'    => 1,
						'total_count'   => 1,
					),
					array( '%d', '%s', '%s', '%d', '%d' )
				);
			}
		}
	}

	public function build_scheduled_landing_page_updates_table() {
		$table_name = $this->table_name_prefix . 'scheduled_landing_page_updates';
		$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
				`id` int(10) AUTO_INCREMENT PRIMARY KEY,
				`city_id` int(10) UNSIGNED NOT NULL,
				`county_id` int(10) UNSIGNED DEFAULT NULL,
				`template_page_id` int(10) UNSIGNED DEFAULT NULL,
				`active` boolean DEFAULT NULL,
				`method` ENUM('update', 'create') NOT NULL DEFAULT 'update'
				) {$this->charset_collate}; ";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public function build_excluded_county_template_pages_table() {
		$table_name = $this->table_name_prefix . 'excluded_county_template_pages';
		$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
				`id` int(10) AUTO_INCREMENT PRIMARY KEY,
				`county_id` int(10) UNSIGNED,
				`template_page_id` int(10) UNSIGNED
				) {$this->charset_collate}; ";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public function build_city_data_table() {
		$table_name = $this->table_name_prefix . 'city_data';
		$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
				`id` int(10) AUTO_INCREMENT PRIMARY KEY,
				`active` boolean NOT NULL default 0,
				`phone` char(20),
				`city_id` int(10) UNSIGNED NOT NULL
				) {$this->charset_collate}; ";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public function build_county_data_table() {
		$table_name = $this->table_name_prefix . 'county_data';
		$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
				`id` int(10) AUTO_INCREMENT PRIMARY KEY,
				`active` boolean NOT NULL default 0,
				`phone` char(20),
				`county_id` int(10) UNSIGNED NOT NULL
				) {$this->charset_collate}; ";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public function build_state_data_table() {
		$table_name = $this->table_name_prefix . 'state_data';
		$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
				`id` int(10) AUTO_INCREMENT PRIMARY KEY,
				`active` boolean NOT NULL default 0,
				`phone` char(20),
				`state_id` int(10) UNSIGNED NOT NULL
				) {$this->charset_collate}; ";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public function build_country_data_table() {
		$table_name = $this->table_name_prefix . 'country_data';
		$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
				`id` int(10) AUTO_INCREMENT PRIMARY KEY,
				`active` boolean NOT NULL default 0,
				`phone` char(20),
				`country_id` int(10) UNSIGNED NOT NULL
				) {$this->charset_collate}; ";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public function build_cities_table() {
		$table_name = $this->table_name_prefix . 'cities';
		$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
				`id` int(10) UNSIGNED NOT NULL PRIMARY KEY,
				`name` varchar(255) NOT NULL,
				`state_id` int(10) UNSIGNED NOT NULL,
				`country_id` int(10) UNSIGNED NOT NULL
				) {$this->charset_collate}; ";
		// $sql.= "INSERT INTO `{$table_name}` (`id`, `name`, `state_id`, `country_id`) VALUES ";
		// $sql.= file_get_contents( $this->data_url . 'cities.sql' );
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public function build_city_county_table() {
		$table_name = $this->table_name_prefix . 'city_county';
		$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
			    `id` int(10) UNSIGNED NOT NULL PRIMARY KEY,
			    `city_id` int(10) UNSIGNED NOT NULL,
			    `county_id` int(10) UNSIGNED NOT NULL
				) {$this->charset_collate}; ";
		// $sql.= "INSERT INTO `{$table_name}` (`id`, `city_id`, `county_id`) VALUES ";
		// $sql.= file_get_contents( $this->data_url . 'city_county.sql' );
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public function build_city_zip_code_table() {
		$table_name = $this->table_name_prefix . 'city_zip_code';
		$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
			    `id` int(10) UNSIGNED NOT NULL PRIMARY KEY,
			    `city_id` int(10) UNSIGNED NOT NULL,
			    `zip_code_id` int(10) UNSIGNED NOT NULL
				) {$this->charset_collate}; ";
		// $sql.= "INSERT INTO `{$table_name}` (`id`, `city_id`, `zip_code_id`) VALUES ";
		// $sql.= file_get_contents( $this->data_url . 'city_zip_code.sql' );
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public function build_counties_table() {
		$table_name = $this->table_name_prefix . 'counties';
		$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
				`id` int(10) UNSIGNED NOT NULL PRIMARY KEY,
				`name` varchar(255) NOT NULL,
				`state_id` int(10) UNSIGNED NOT NULL
				) {$this->charset_collate}; ";
		// $sql.= "INSERT INTO `{$table_name}` (`id`, `name`, `state_id`) VALUES ";
		// $sql.= file_get_contents( $this->data_url . 'counties.sql' );
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public function build_states_table() {
		$table_name = $this->table_name_prefix . 'states';
		$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
				`id` int(10) UNSIGNED NOT NULL PRIMARY KEY,
				`name` varchar(255) NOT NULL,
				`postal_code` char(2) NOT NULL,
				`state_code` char(2) NOT NULL,
				`country_id` int(10) UNSIGNED NOT NULL
				) {$this->charset_collate}; ";
		$sql.= "INSERT INTO `{$table_name}` (`id`, `name`, `postal_code`, `state_code`, `country_id`) VALUES ";
		$sql.= file_get_contents( $this->data_url . 'states.sql' );
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public function build_zip_codes_table() {
		$table_name = $this->table_name_prefix . 'zip_codes';
		$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
				`id` int(10) UNSIGNED NOT NULL PRIMARY KEY,
				`zip_code` varchar(10) DEFAULT NULL
				) {$this->charset_collate}; ";
		// $sql.= "INSERT INTO `{$table_name}` (`id`, `zip_code`) VALUES ";
		// $sql.= file_get_contents( $this->data_url . 'zip_codes.sql' );
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public function build_countries_table() {
		$table_name = $this->table_name_prefix . 'countries';
		$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
				`id` int(10) UNSIGNED NOT NULL PRIMARY KEY,
				`name` varchar(255) NOT NULL,
				`abbreviation` char(2) NOT NULL
				) {$this->charset_collate}; ";
		$sql.= "INSERT INTO `{$table_name}` (`id`, `name`, `abbreviation`) VALUES ";
		$sql.= file_get_contents( $this->data_url . 'countries.sql' );
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public function build_state_demographics_table() {
		$table_name = $this->table_name_prefix . 'state_demographics';
		$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
		        `state_id` int(10) UNSIGNED NOT NULL PRIMARY KEY,
		        `population` int UNSIGNED DEFAULT NULL,
		        `households` int UNSIGNED DEFAULT NULL,
		        `median_income` int UNSIGNED DEFAULT NULL,
		        `land_area` bigint UNSIGNED DEFAULT NULL,
		        `water_area` bigint UNSIGNED DEFAULT NULL,
		        `fbi_data` blob DEFAULT NULL,
		        `population_data` blob DEFAULT NULL,
		        `education_data` blob DEFAULT NULL
				) {$this->charset_collate}; ";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public function build_county_demographics_table() {
		$table_name = $this->table_name_prefix . 'county_demographics';
		$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
		        `county_id` int(10) UNSIGNED NOT NULL PRIMARY KEY,
		        `state_id` int UNSIGNED NOT NULL,
		        `population` int UNSIGNED DEFAULT NULL,
		        `households` int UNSIGNED DEFAULT NULL,
		        `median_income` int UNSIGNED DEFAULT NULL,
		        `land_area` bigint UNSIGNED DEFAULT NULL,
		        `water_area` bigint UNSIGNED DEFAULT NULL,
		        `climate_data` blob DEFAULT NULL,
		        `fbi_data` blob DEFAULT NULL
				) {$this->charset_collate}; ";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public function build_city_demographics_table() {
		$table_name = $this->table_name_prefix . 'city_demographics';
		$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
		        `city_id` int(10) UNSIGNED NOT NULL PRIMARY KEY,
		        `state_id` int UNSIGNED NOT NULL,
		        `type` char(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		        `geo_id` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		        `population` int UNSIGNED DEFAULT NULL,
		        `households` int UNSIGNED DEFAULT NULL,
		        `median_income` int UNSIGNED DEFAULT NULL,
		        `land_area` bigint UNSIGNED DEFAULT NULL,
		        `water_area` bigint UNSIGNED DEFAULT NULL,
		        `latitude` decimal(10,8) DEFAULT NULL,
		        `longitude` decimal(11,8) DEFAULT NULL,
		        `climate_data` blob DEFAULT NULL,
		        `fbi_data` blob DEFAULT NULL
				) {$this->charset_collate}; ";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public function build_logs_table() {
		$table_name = $this->table_name_prefix . 'logs';
		$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
			    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			    `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			    `category` ENUM('note', 'error', 'warning') NOT NULL DEFAULT 'note',
			    `method` varchar(255) NOT NULL,
			    `message` varchar(255) NOT NULL
				) {$this->charset_collate}; ";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}


}