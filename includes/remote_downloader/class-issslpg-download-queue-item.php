<?php

class ISSSLPG_Download_Queue_Item {

	private $wpdb;
	private $unit_id;
	private $unit_column;
	private $unit_category;
	private $table_name;

	public function __construct( $unit_category, $unit_id, $table_name ) {
		global $wpdb;
		$this->wpdb          = $wpdb;
		$this->unit_id       = (int)$unit_id;
		$this->unit_category = $unit_category;
		$this->table_name    = $table_name;
	}

	public function maybe_create_item( $item_count, $total_item_count ) {
		if ( $this->does_item_exist() ) {
			return true;
		}
		return $this->create_item( $item_count, $total_item_count );
	}

	public function update_or_create( $item_count, $total_item_count ) {
		if ( $this->does_item_exist() ) {
			return $this->update_item( $item_count );
		}
		return $this->create_item( $item_count, $total_item_count );
	}

	private function create_item( $item_count, $total_item_count ) {
		return $this->wpdb->replace(
			"{$this->wpdb->prefix}issslpg_download_queue",
			array(
				'unit_id'       => (int)$this->unit_id,
				'unit_category' => $this->unit_category,
				'table_name'    => $this->table_name,
				'item_count'    => (int)$item_count,
				'total_count'   => (int)$total_item_count,
			),
			array( '%d', '%s', '%s', '%d', '%d' )
		);
	}

	public function update_item( $item_count ) {
		return $this->wpdb->update(
			"{$this->wpdb->prefix}issslpg_download_queue",
			array(
				'item_count' => (int)$item_count,
			),
			array(
				'unit_id'    => (int)$this->unit_id,
				'table_name' => $this->table_name,
			),
			array( '%d' ),
			array( '%d', '%s' )
		);
	}

	// public function delete_item() {
	// 	return $this->wpdb->delete(
	// 		{$this->wpdb->prefix}issslpg_download_queue,
	// 		array( 'unit_id' => (int)$this->unit_id ),
	// 		'%d'
	// 	);
	// }

	public function get_progress() {
		$current = $this->get_item_count();
		$total = $this->get_item_total();
		return floor( ( $current / $total ) * 100 ); // Calculate percentage
	}

	public function get_item() {
		$queue_table_name = "{$this->wpdb->prefix}issslpg_download_queue";
		return $this->wpdb->get_row( "SELECT * FROM {$queue_table_name} WHERE unit_id = {$this->unit_id} AND table_name = '{$this->table_name}'" );
	}

	public function get_table_name() {
		$item = $this->get_item();
		return is_null( $item ) ? null : $item->table_name;
	}

	public function get_item_count() {
		$item = $this->get_item();
		return is_null( $item ) ? 0 : $item->item_count;
	}

	public function get_total_item_count() {
		$item = $this->get_item();
		return is_null( $item ) ? null : $item->total_count;
	}

	public function does_item_exist() {
		$item = $this->get_item();
		return ( $item );
	}

	public function is_complete() {
		$item = $this->get_item();
		if ( ! $item ) {
			return null;
		}
		return ( $item->item_count == $item->total_count );
	}

}