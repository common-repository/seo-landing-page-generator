<?php

use WeDevs\ORM\WP\CountyData as CountyData;

class ISSSLPG_Helpers {

	static public function is_content_randomizer_plugin_active() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		return is_plugin_active( 'seo-content-randomizer/seo-content-randomizer.php' )
		       || is_plugin_active( 'seo-content-randomizer-premium/seo-content-randomizer.php' );
	}

	static protected function are_simulated_addon_features_active() {
		return defined( 'ISSSLPG_ACTIVATE_ADDON_FEATURES' ) && ISSSLPG_ACTIVATE_ADDON_FEATURES;
	}

	static public function is_demographics_usage_allowed() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( self::are_simulated_addon_features_active() ) {
			return true;
		}

		return is_plugin_active( 'seo-landing-page-generator-demographics/seo-landing-page-generator-demographics.php' )
		       || is_plugin_active( 'seo-landing-page-generator-demographics-premium/seo-landing-page-generator-demographics.php' );
	}

	static public function is_schema_usage_allowed() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( self::are_simulated_addon_features_active() ) {
			return true;
		}

		return is_plugin_active( 'seo-landing-page-generator-schema/seo-landing-page-generator-schema.php' )
		       || is_plugin_active( 'seo-landing-page-generator-schema-premium/seo-landing-page-generator-schema.php' );
	}

	static public function is_faq_usage_allowed() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( self::are_simulated_addon_features_active() ) {
			return true;
		}

		return is_plugin_active( 'seo-landing-page-generator-faq/seo-landing-page-generator-faq.php' )
		       || is_plugin_active( 'seo-landing-page-generator-faq-premium/seo-landing-page-generator-faq.php' );
	}

	static public function is_cta_button_usage_allowed() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( self::are_simulated_addon_features_active() ) {
			return true;
		}

		$cta_button_plugin_active = is_plugin_active( 'seo-landing-page-generator-cta-button/seo-landing-page-generator-cta-button.php' )
		                            || is_plugin_active( 'seo-landing-page-generator-cta-button-premium/seo-landing-page-generator-cta-button.php' );

		$original_installation_version = get_option( 'issslpg_original_installation_version' );
		$cta_button_usage_allowed = version_compare( $original_installation_version, '1.28.0', '<' );

		return $cta_button_plugin_active || $cta_button_usage_allowed;
	}

	static public function is_local_content_usage_allowed() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( self::are_simulated_addon_features_active() ) {
			return true;
		}

		$local_content_plugin_active = is_plugin_active( 'seo-landing-page-generator-local-content/seo-landing-page-generator-local-content.php' )
		                               || is_plugin_active( 'seo-landing-page-generator-local-content-premium/seo-landing-page-generator-local-content.php' );

		$original_installation_version = get_option( 'issslpg_original_installation_version' );
		$local_content_usage_allowed = version_compare( $original_installation_version, '1.29.0', '<' );

		return $local_content_plugin_active || $local_content_usage_allowed;
	}

	static public function is_white_labeled() {
		return defined( 'ISSSLPG_WHITELABEL' ) && ISSSLPG_WHITELABEL;
	}

	static public function is_content_randomizer_page() {
		if ( self::is_content_randomizer_plugin_active() ) {
			return ISSSCR_Helpers::is_randomizer_page();
		}

		return false;
	}

	static public function has_auto_content_replacement_enabled() {
		if ( self::is_content_randomizer_plugin_active() ) {
			$post_type = ISSSCR_Meta_Data::get_post_type();
			return ISSSCR_Options::get_setting( "{$post_type}_auto_content_replace", true );
		}

		return false;
	}

	static function post_exists_by_slug( $post_slug, $post_type = 'post' ) {
		$wp_query = new WP_Query( array(
			'post_type'      => $post_type,
			'post_status'    => 'any',
			'name'           => $post_slug,
			'posts_per_page' => 1,
		) );

		if ( ! $wp_query->have_posts() ) {
			wp_reset_postdata();
			return false;
		}

		$wp_query->the_post();
		$post_id = $wp_query->post->ID;
		wp_reset_postdata();

		return $post_id;
	}

	static public function is_plan( $plan_handle ) {
		if ( defined( 'ISSSLPG_PLAN' ) ) {
			return ( ISSSLPG_PLAN === $plan_handle );
		}

		return issslpg_fs()->is_plan_or_trial( $plan_handle, true );
	}

	static public function is_simulated_plan() {
		return defined( 'ISSSLPG_PLAN' );
	}

	static public function get_county_limit( $multiplier = 1 ) {
		$county_limit = 1;

		if ( self::is_plan( 'starter' ) ) {
			$county_limit = 3;
		}
		elseif ( self::is_plan( 'pro' ) ) {
			$county_limit = 10;
		}
		elseif ( self::is_plan( 'enterprise' ) ) {
			$county_limit = 9999;
		}

		return $county_limit * $multiplier;
	}

	static public function is_county_limit_reached() {
		$active_counties_count = CountyData::where( 'active', '1' )->count();
		return ( $active_counties_count > self::get_county_limit() );
	}

	static public function get_dynamic_panel_limit( $multiplier = 1 ) {
		$panel_limit = 10;

		if ( self::is_plan( 'starter' ) ) {
			$panel_limit = 15;
		}
		elseif ( self::is_plan( 'pro' ) ) {
			$panel_limit = 20;
		}
		elseif ( self::is_plan( 'enterprise' ) ) {
			$panel_limit = 9999;
		}

		return $panel_limit * $multiplier;
	}

	static public function get_repeater_box_rows_limit( $multiplier = 1 ) {
		$row_limit = 3;

		if ( self::is_plan( 'starter' ) ) {
			$row_limit = 10;
		}
		elseif ( self::is_plan( 'pro' ) ) {
			$row_limit = 20;
		}
		elseif ( self::is_plan( 'enterprise' ) ) {
			$row_limit = 9999;
		}

		return $row_limit * $multiplier;
	}

	static public function get_keyword_limit( $multiplier = 1 ) {
		$keyword_limit = 3;

		if ( self::is_plan( 'starter' ) ) {
			$keyword_limit = 10;
		}
		elseif ( self::is_plan( 'pro' ) ) {
			$keyword_limit = 30;
		}
		elseif ( self::is_plan( 'enterprise' ) ) {
			$keyword_limit = 9999;
		}

		return $keyword_limit * $multiplier;
	}

	static public function reduce_array_by_dynamic_panel_limit( array $array, $multiplier = 1 ) {
		$panel_limit = self::get_dynamic_panel_limit( $multiplier );
		return array_slice( $array, 0, $panel_limit, true );
	}

	static public function reduce_array_by_rows_limit( array $array, $multiplier = 1 ) {
		$rows_limit = self::get_repeater_box_rows_limit( $multiplier );
		return array_slice( $array, 0, $rows_limit, true );
	}

	static public function reduce_array_by_keyword_limit( array $array, $multiplier = 1 ) {
		$keyword_limit = self::get_keyword_limit( $multiplier );
		return array_slice( $array, 0, $keyword_limit );
	}

	static public function reduce_array_by_county_limit( array $array, $multiplier = 1 ) {
		$rows_limit = self::get_county_limit( $multiplier );
		return array_slice( $array, 0, $rows_limit, true );
	}

}