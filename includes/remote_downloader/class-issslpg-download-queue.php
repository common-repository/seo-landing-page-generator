<?php

class ISSSLPG_Download_Queue {

	private $wpdb;
	private $download_queue_table_name;
	private $unit_id;
	private $unit_categories;
	private $table_names;

	public function __construct( $unit_categories, $unit_id ) {
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->download_queue_table_name = "{$this->wpdb->prefix}issslpg_download_queue";
		$this->unit_categories = $unit_categories;
		$this->unit_id = (int)$unit_id;
		$this->table_names = array(
			'locations' => array(
				'zip_codes',
				'city_zip_code',
				'cities',
				'city_county',
				'counties',
			),
			'demographics' => array(
				'state_demographics',
				'county_demographics',
				'city_demographics',
			),
		);
	}

	public function get_current_item() {
		return new ISSSLPG_Download_Queue_Item( $this->get_pending_unit_category(), $this->unit_id, $this->get_pending_table_name() );
	}

	public function maybe_create_queue_items() {
		foreach ( $this->unit_categories as $unit_category ) {
			$table_names = $this->table_names[ $unit_category ];
			foreach( $table_names as $table_name ) {
				$queue_item = new ISSSLPG_Download_Queue_Item( $unit_category, $this->unit_id, $table_name );
				if ( $queue_item->does_item_exist() ) {
					continue;
				}
				$remote_location_data = new ISSSLPG_Remote_Data();
				$total_item_count = $remote_location_data->load_total_item_count( $unit_category, $this->unit_id, $table_name );
				if ( $total_item_count ) {
					$queue_item->maybe_create_item( 0, $total_item_count );
				}
			}
		}
	}

	public function get_pending_table_name() {
		$pending_unit_category = $this->get_pending_unit_category();
		$pending_table_name = $this->wpdb->get_var( "SELECT table_name FROM {$this->download_queue_table_name} WHERE unit_category = '{$pending_unit_category}' AND unit_id = {$this->unit_id} AND item_count != total_count" );
		if ( ! $pending_table_name ) {
			return $this->table_names[ $pending_unit_category ][ 0 ];
		}

		return $pending_table_name;
	}

	public function get_pending_unit_category() {
		$unit_categories_query_part = array_map( function( $unit_category ) { return "'{$unit_category}'"; }, $this->unit_categories );
		$unit_categories_query_part = join( ',', $unit_categories_query_part );
		$pending_unit_category = $this->wpdb->get_var( "SELECT unit_category FROM {$this->download_queue_table_name} WHERE unit_category IN ({$unit_categories_query_part}) AND unit_id = {$this->unit_id} AND item_count != total_count" );
		if ( ! $pending_unit_category ) {
			return $this->unit_categories[0];
		}

		return $pending_unit_category;
	}

	public function get_progress() {
		$unit_categories_query_part = array_map( function( $unit_category ) { return "'{$unit_category}'"; }, $this->unit_categories );
		$unit_categories_query_part = join( ',', $unit_categories_query_part );
		$queue_items = $this->wpdb->get_results( "SELECT item_count, total_count FROM {$this->download_queue_table_name} WHERE unit_category IN ({$unit_categories_query_part}) AND unit_id = {$this->unit_id}" );
		if ( ! $queue_items ) {
			return 0;
		}
		$all_item_count = 0;
		$all_total_count = 0;
		foreach ( $queue_items as $queue_item ) {
			$all_item_count += $queue_item->item_count;
			$all_total_count += $queue_item->total_count;
		}

		return floor( ( $all_item_count / $all_total_count ) * 100 ); // Calculate percentage
	}

	public function is_complete() {
		return ( 100 == $this->get_progress() );
	}

}