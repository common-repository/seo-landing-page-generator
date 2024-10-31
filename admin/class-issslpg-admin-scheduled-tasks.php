<?php



class ISSSLPG_Admin_Scheduled_Tasks {

	static private function add_task( $where, $where_format ) {
		global $wpdb;
		$wpdb->delete(
				"{$wpdb->prefix}issslpg_scheduled_landing_page_updates",
				$where,
				$where_format
		);
		return $wpdb->replace(
				"{$wpdb->prefix}issslpg_scheduled_landing_page_updates",
				$where,
				$where_format
		);
	}

	static public function delete_tasks_by_template_page_id( $template_page_id ) {
		global $wpdb;
		$wpdb->delete(
			"{$wpdb->prefix}issslpg_scheduled_landing_page_updates",
			array( 'template_page_id' => $template_page_id )
		);
	}

	static public function add_landing_pages_to_update( $city_id, $county_id, $template_page_id ) {
		self::add_task(
			array(
					'city_id'          => $city_id,
					'county_id'        => $county_id,
					'template_page_id' => $template_page_id,
					'method'           => 'update',
			),
			array( '%d', '%d', '%d', '%s' )
		);
		ISSSLPG_Logger::log( 'Scheduled landing page to update: ' . get_the_title( $template_page_id ), __METHOD__ );
	}

	static public function add_custom_location_landing_pages( $county_id, $custom_location_hash, $status ) {
		$template_pages = new WP_Query( array(
				'post_type'      => 'issslpg-template',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
		) );
		while ( $template_pages->have_posts() ) {
			$template_pages->the_post();
			self::add_task(
				array(
					'county_id'            => $county_id,
					'template_page_id'     => get_the_ID(),
					'active'               => $status,
					'method'               => 'update',
					'custom_location_hash' => $custom_location_hash,
				),
				array( '%d', '%d', '%d', '%s', '%s' )
			);
		}
		wp_reset_postdata();
	}

	static public function add_landing_pages_to_activate( $city_id, $county_id, $status ) {

		if ( ! $status ) {
			self::add_task(
				array(
					'city_id'   => $city_id,
					'county_id' => $county_id,
					'active'    => $status,
					'method'    => 'create',
				),
				array( '%d', '%d', '%d', '%s' )
			);
		}

		else {
			$template_pages = new WP_Query( array(
				'post_type'      => 'issslpg-template',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			) );
			while ( $template_pages->have_posts() ) {
				$template_pages->the_post();
				self::add_task(
					array(
						'city_id'          => $city_id,
						'county_id'        => $county_id,
						'template_page_id' => get_the_ID(),
						'active'           => $status,
						'method'           => 'update',
					),
					array( '%d', '%d', '%d', '%d', '%s' )
				);
			}
			wp_reset_postdata();
		}
	}

	static public function update_landing_pages($landing_page_amount = 5) {
		global $wpdb;
//		$results = $wpdb->get_results( "
//			SELECT *
//			FROM {$wpdb->prefix}issslpg_scheduled_landing_page_updates
//			WHERE method = 'update'
//			AND template_page_id IS NOT NULL
//			AND city_id IS NOT NULL
//			LIMIT {$landing_page_amount}
//		" );

		$results = self::get_priority_landing_pages( $landing_page_amount );

		foreach ( $results as $result ) {
			ISSSLPG_Landing_Page::create_or_update_correlating_landing_pages_by_city( $result->city_id, $result->county_id, $result->template_page_id );
			$wpdb->delete( "{$wpdb->prefix}issslpg_scheduled_landing_page_updates", array( 'id' => $result->id, 'method' => 'update' ) );
		}
	}

	static public function get_priority_landing_pages( $limit = 1 ) {
		global $wpdb;
		$template_page_priority = trim( ISSSLPG_Options::get_setting( 'template_page_priority' ) );

		if ( ! empty( $template_page_priority ) ) {
			$template_page_priority_ids = explode( ',', $template_page_priority );
			if ( is_array( $template_page_priority_ids ) ) {
//				error_log( print_r( $template_page_priority_ids, TRUE ) );
				foreach ( $template_page_priority_ids as $template_page_id ) {
					if ( ! is_numeric( $template_page_id ) ) {
						continue;
					}
					$results = $wpdb->get_results( "
						SELECT *
						FROM {$wpdb->prefix}issslpg_scheduled_landing_page_updates
						WHERE method = 'update'
						AND template_page_id = '{$template_page_id}'
						AND city_id IS NOT NULL
						LIMIT {$limit}
					" );
//					error_log( print_r( $results, TRUE ) );
					if ( ! empty( $results ) ) {
//						error_log( 'Building priority pages...' );
						break;
					}
				}
			}
		}

		if ( empty( $results ) ) {
//			error_log( 'All priority pages built!' );
			$results = $wpdb->get_results( "
				SELECT *
				FROM {$wpdb->prefix}issslpg_scheduled_landing_page_updates
				WHERE method = 'update'
				AND template_page_id IS NOT NULL
				AND city_id IS NOT NULL
				LIMIT {$limit}
			" );
		}

		return $results;
	}

	static public function bulk_update_landing_pages() {
		global $wpdb;
		$results = $wpdb->get_results( "
			SELECT *
			FROM {$wpdb->prefix}issslpg_scheduled_landing_page_updates
			WHERE method = 'update'
			AND template_page_id IS NULL
			AND city_id IS NOT NULL
			LIMIT 1
		" );

//		$results = get_priority_landing_pages( 1 );

		foreach ( $results as $result ) {
			ISSSLPG_Landing_Page::create_or_update_correlating_landing_pages_by_city( $result->city_id );
			$wpdb->delete( "{$wpdb->prefix}issslpg_scheduled_landing_page_updates", array( 'id' => $result->id, 'method' => 'update' ) );
		}
	}

	static public function update_custom_location_landing_pages() {
		global $wpdb;
		$results = $wpdb->get_results( "
			SELECT *
			FROM {$wpdb->prefix}issslpg_scheduled_landing_page_updates
			WHERE method = 'update'
			AND county_id IS NOT NULL
			AND custom_location_hash IS NOT NULL
			LIMIT 4
		" );

		foreach ( $results as $result ) {
			$county_data = new ISSSLPG_County_Data( $result->county_id );
			$custom_locations = $county_data->custom_locations;
			if ( $custom_locations ) {
				foreach ( $custom_locations as $custom_location ) {
					if ( $custom_location['hash'] == $result->custom_location_hash && $result->active == '1' ) {
						ISSSLPG_Landing_Page::create_or_update_correlating_landing_pages_by_custom_locations( $custom_location, $result->county_id, $result->template_page_id );
						$wpdb->delete( "{$wpdb->prefix}issslpg_scheduled_landing_page_updates", array( 'id' => $result->id, 'method' => 'update' ) );
					}
				}
			}

			if ( $result->active == '0' ) {
				ISSSLPG_Landing_Page::remove_custom_location_landing_pages( $result->template_page_id, $result->custom_location_hash );
				$wpdb->delete( "{$wpdb->prefix}issslpg_scheduled_landing_page_updates", array( 'id' => $result->id, 'method' => 'update' ) );
			}
		}
	}

	static public function remove_custom_location_landing_page( $custom_location_hash ) {
		global $wpdb;
		$wpdb->delete( "{$wpdb->prefix}issslpg_scheduled_landing_page_updates", array( 'custom_location_hash' => $custom_location_hash ) );
	}

	static public function change_landing_pages_status() {
		global $wpdb;
		$results = $wpdb->get_results( "
			SELECT *
			FROM {$wpdb->prefix}issslpg_scheduled_landing_page_updates
			WHERE method = 'create'
			AND active = '0'
			AND city_id IS NOT NULL
			LIMIT 5
		" );

		foreach ( $results as $result ) {
			$county_data = new ISSSLPG_County_Data( $result->county_id );
			$city_data = new ISSSLPG_City_Data( $result->city_id );
			if ( is_object( $county_data ) && is_object( $city_data ) ) {
//				if ( $city_data->status !== $result->active ) {
				$city_data->update(
						array( 'active' => $result->active ),
						$county_data->county_object
				);
//				}
			}

			$wpdb->delete( "{$wpdb->prefix}issslpg_scheduled_landing_page_updates", array( 'id' => $result->id, 'method' => 'create' ) );
		}
	}

}