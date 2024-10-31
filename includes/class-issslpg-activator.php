<?php

/**
 * Fired during plugin activation
 *
 * @link       https://intellasoftplugins.com
 * @since      1.0.0
 *
 * @package    ISSSLPG
 * @subpackage ISSSLPG/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    ISSSLPG
 * @subpackage ISSSLPG/includes
 * @author     Ruven Pelka <ruven.pelka@gmail.com>
 */
class ISSSLPG_Activator {

	public static function activate() {
		self::create_sitemap_page();
		self::create_us_location_db_tables();
		self::set_flush_rewrite_rules_flag();
		self::add_sample_content();
	}

	public static function create_sitemap_page() {
		$slug = 'sitemap';
		if ( ! ISSSLPG_Helpers::post_exists_by_slug( $slug, 'page' ) ) {
			wp_insert_post( array(
					'post_type'    => 'page',
					'post_title'   => 'Sitemap',
					'post_name'    => $slug,
					'post_content' => '[iss_sitemap]'
			) );
		}
//		for ( $i = 1; $i <= 99; $i++ ) {
//			if ( ISSSLPG_Helpers::post_exists_by_slug( $slug ) ) {
//				$slug = "{$slug}-{$i}";
//			} else {
//				wp_insert_post( array(
//						'post_type'    => 'page',
//						'post_title'   => 'Sitemap',
//						'post_name'    => $slug,
//						'post_content' => '[iss_sitemap] test'
//				) );
//				break;
//			}
//		}
	}

	public static function create_us_location_db_tables() {
		$db_tables = new ISSSLPG_Database_Tables();
		$db_tables->drop_tables(); // In case the tables already exist
		$db_tables->build_tables();
	}

	public static function set_flush_rewrite_rules_flag() {
		if ( ! get_option( 'issslpg_flush_rewrite_rules_flag' ) ) {
			add_option( 'issslpg_flush_rewrite_rules_flag', true );
		}
	}

	public static function add_sample_content() {
		ISSSLPG_ISSSCR_Sample_Content::add_sample_service_template_page();
	}

}
