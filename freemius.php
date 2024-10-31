<?php

if ( ! function_exists( 'issslpg_fs' ) ) {
	// Create a helper function for easy SDK access.
	function issslpg_fs() {
		global $issslpg_fs;

		if ( ! isset( $issslpg_fs ) ) {
			// Activate multisite network integration.
			if ( ! defined( 'WP_FS__PRODUCT_2543_MULTISITE' ) ) {
				define( 'WP_FS__PRODUCT_2543_MULTISITE', true );
			}

			// Include Freemius SDK.
			require_once dirname(__FILE__) . '/freemius/start.php';

			$issslpg_fs = fs_dynamic_init( array(
				'id'                  => '2543',
				'slug'                => 'seo-landing-page-generator',
				'type'                => 'plugin',
				'public_key'          => 'pk_73bea3481b09c25a34a76685a2db9',
				'is_premium'          => true,
				// If your plugin is a serviceware, set this option to false.
				'has_premium_version' => true,
				'has_addons'          => true,
				'has_paid_plans'      => true,
				'trial'               => array(
					'days'               => 14,
					'is_require_payment' => true,
				),
				'has_affiliation'     => 'all',
				'menu'                => array(
					'slug'           => 'issslpg_location_settings',
					'support'        => false,
				),
			) );
		}

		return $issslpg_fs;
	}

	// Init Freemius.
	issslpg_fs();
	// Signal that SDK was initiated.
	do_action( 'issslpg_fs_loaded' );
}