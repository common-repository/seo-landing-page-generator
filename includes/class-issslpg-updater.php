<?php

/**
 * Fired after plugin update
 *
 * @link       https://intellasoftplugins.com
 * @since      1.4.10
 *
 * @package    ISSSLPG
 * @subpackage ISSSLPG/includes
 */

/**
 * Fired after plugin update.
 *
 * This class defines all code necessary to run after a plugin update.
 *
 * @since      1.4.10
 * @package    ISSSLPG
 * @subpackage ISSSLPG/includes
 * @author     Ruven Pelka <ruven.pelka@gmail.com>
 */
class ISSSLPG_Updater {

	public function __construct() {
		$plugin_version = get_option( 'issslpg_version', 0 );

		// Set original installation version
		$original_installation_version = $plugin_version ? $plugin_version : ISSSLPG_VERSION;
		add_option( 'issslpg_original_installation_version', $original_installation_version );

		if ( version_compare( $plugin_version, ISSSLPG_VERSION, '<' ) ) {
			$this->update_1();
		}
		if ( version_compare( $plugin_version, '1.4.12', '<' ) ) {
			$this->update_2();
		}
		if ( version_compare( $plugin_version, '1.5.10', '<' ) ) {
			$this->update_3();
		}
//		if ( version_compare( $plugin_version, '1.12.1', '<' ) ) {
//			$this->update_4();
//		}
		if ( version_compare( $plugin_version, '1.26.0', '<' ) ) {
			$this->update_5();
		}
		if ( version_compare( $plugin_version, '1.26.1', '<' ) ) {
			$this->update_6();
		}
		if ( version_compare( $plugin_version, '1.26.4', '<' ) ) {
			$this->update_7();
		}
		if ( version_compare( $plugin_version, '1.27.0', '<' ) ) {
			$this->update_8();
		}
		if ( $plugin_version != 0 && version_compare( $plugin_version, '1.31.0', '<' ) ) {
			$this->update_9();
		}
		if ( version_compare( $plugin_version, '1.31.0', '<' ) ) {
			$this->update_10();
		}
		if ( version_compare( $plugin_version, '1.31.2', '<' ) ) {
			$this->update_11();
		}
		if ( version_compare( $plugin_version, '1.34.0', '<' ) ) {
			$this->update_12();
		}
		if ( version_compare( $plugin_version, '1.36.2', '<' ) ) {
			$this->update_13();
		}
		if ( version_compare( $plugin_version, '1.37.0', '<' ) ) {
			$this->update_14();
		}

		update_option( 'issslpg_version', ISSSLPG_VERSION );
	}

	public function update_1() {
		$db_tables = new ISSSLPG_Database_Tables();
		$db_tables->drop_tables(); // In case the tables already exist
		$db_tables->build_tables();
	}

	public function update_2() {
		global $wpdb;
		$table_name = "{$wpdb->prefix}issslpg_scheduled_landing_page_updates";
		$wpdb->query( "ALTER TABLE {$table_name} MODIFY COLUMN county_id INT(10) UNSIGNED DEFAULT NULL;" );
		$wpdb->query( "ALTER TABLE {$table_name} MODIFY COLUMN template_page_id INT(10) UNSIGNED DEFAULT NULL;" );
	}

	public function update_3() {
		global $wpdb;
		$table_name = "{$wpdb->prefix}issslpg_scheduled_landing_page_updates";

		$does_active_column_exist = $wpdb->get_results( "SHOW COLUMNS FROM {$table_name} LIKE 'active'" );
		$does_method_column_exist = $wpdb->get_results( "SHOW COLUMNS FROM {$table_name} LIKE 'method'" );

		if ( ! $does_active_column_exist ) {
			$wpdb->query( "ALTER TABLE {$table_name} ADD active BOOLEAN DEFAULT NULL;" );
		}
		if ( ! $does_method_column_exist ) {
			$wpdb->query( "ALTER TABLE {$table_name} ADD method ENUM('update', 'create') NOT NULL DEFAULT 'update';" );
		}
	}

//	public function update_4() {
//		global $wpdb;
//		$new_table_name = "{$wpdb->prefix}issslpg_scheduled_landing_page_updates";
//		if ( $wpdb->get_var("SHOW TABLES LIKE '{$new_table_name}'") != $new_table_name ) {
//			$old_table_name = "{$wpdb->prefix}issslpg_scheduled_landing_page_updates";
//			$wpdb->query( "ALTER TABLE {$old_table_name} RENAME TO {$new_table_name};" );
//		}
//	}

	public function update_5() {
		global $wpdb;

		$table_name = "{$wpdb->prefix}issslpg_county_data";
		$wpdb->query( "ALTER TABLE {$table_name} ADD custom_locations char(20) DEFAULT NULL;" );

		$table_name = "{$wpdb->prefix}issslpg_scheduled_landing_page_updates";
		$wpdb->query( "ALTER TABLE {$table_name} ADD custom_location_hash TEXT DEFAULT NULL;" );
		$wpdb->query( "ALTER TABLE {$table_name} MODIFY COLUMN city_id int(10) DEFAULT NULL;" );
	}

	public function update_6() {
		global $wpdb;

		$table_name = "{$wpdb->prefix}issslpg_county_data";
		$wpdb->query( "ALTER TABLE {$table_name} MODIFY COLUMN custom_locations LONGTEXT DEFAULT NULL;" );

		$table_name = "{$wpdb->prefix}issslpg_scheduled_landing_page_updates";
		$wpdb->query( "ALTER TABLE {$table_name} MODIFY COLUMN custom_location_hash char(20) DEFAULT NULL;" );
	}

	public function update_7() {
		global $wpdb;
		$table_name = "{$wpdb->prefix}issslpg_scheduled_landing_page_updates";
		$wpdb->query( "ALTER TABLE {$table_name} MODIFY COLUMN city_id int(10) DEFAULT NULL;" );
	}

	public function update_8() {
		$default_phone = ISSSLPG_Options::get_setting( 'default_phone' );
		ISSSLPG_Options::update_setting( 'landing_page_default_phone', $default_phone );
	}

	public function update_9() {
		// Alter cities table
		global $wpdb;
		$table_name = "{$wpdb->prefix}issslpg_cities";
		$does_country_id_column_exist = $wpdb->get_results( "SHOW COLUMNS FROM {$table_name} LIKE 'country_id'" );
		if ( ! $does_country_id_column_exist ) {
			$wpdb->query( "ALTER TABLE {$table_name} ADD country_id int(10) UNSIGNED NOT NULL;" );
			$wpdb->query( "UPDATE {$table_name} SET country_id = 1;" );
		}

		// Pre-seed US data, if it's already installed by previous version
		$db_tables = new ISSSLPG_Database_Tables();
		$db_tables->seed_download_queue_with_us_location_data();

		// Flag US as active, if previously activated cities exist
		$table_name = "{$wpdb->prefix}issslpg_city_data";
		$does_active_us_city_exist = $wpdb->get_results( "SELECT id FROM {$table_name} where active = 1 LIMIT 1" );
		if ( $does_active_us_city_exist ) {
			$country_data = new ISSSLPG_Country_Data( 1 );
			$country_data->update( array(
				'active' => true,
			) );
		}
	}

	public function update_10() {
		global $wpdb;

		$db_tables = new ISSSLPG_Database_Tables();
		$db_tables->drop_tables();
		$db_tables->build_tables();

		$table_name = "{$wpdb->prefix}issslpg_state_data";
		$wpdb->query( "ALTER TABLE {$table_name} ADD office_google_pid varchar(255) DEFAULT NULL;" );

		$table_name = "{$wpdb->prefix}issslpg_county_data";
		$wpdb->query( "ALTER TABLE {$table_name} ADD office_google_pid varchar(255) DEFAULT NULL;" );
	}

	public function update_11() {
		if ( ! get_option( 'issslpg_flush_rewrite_rules_flag' ) ) {
			add_option( 'issslpg_flush_rewrite_rules_flag', true );
		}
	}

	public function update_12() {
		// Add Settings column to county_data table
		global $wpdb;
		$table_name = "{$wpdb->prefix}issslpg_county_data";
		$wpdb->query( "ALTER TABLE {$table_name} ADD settings LONGTEXT DEFAULT NULL;" );

		// Migrate business info settings
		$company_phone = ISSSLPG_Options::get_setting( 'company_phone' );
		ISSSLPG_Options::update_setting( 'company_phone', $company_phone, 'iss_company_info_settings' );
		$company_email = ISSSLPG_Options::get_setting( 'default_email' );
		ISSSLPG_Options::update_setting( 'company_email', $company_email, 'iss_company_info_settings' );
	}

	public function update_13() {
		// Migrate business info settings
		$company_description = ISSSLPG_Options::get_setting( 'company_desciption' );
		ISSSLPG_Options::update_setting( 'company_description', $company_description, 'iss_company_info_settings' );
	}

	public function update_14() {
		update_option( 'issslpg_seed_download_queue_with_demographics_slots', 1 );
	}

}