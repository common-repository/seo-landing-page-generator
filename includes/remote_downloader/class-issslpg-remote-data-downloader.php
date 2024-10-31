<?php

class ISSSLPG_Remote_Data_Downloader {

	private $unit_categories;
	private $unit_id;
	private $seeder;
	private $queue;
	private $remote_location_data;

	public function __construct( $unit_categories, $unit_id ) {
		$this->unit_categories = $unit_categories;
		$this->unit_id = (int)$unit_id;
		$this->seeder = new ISSSLPG_Data_Seeder();
		$this->queue = new ISSSLPG_Download_Queue( $this->unit_categories, $this->unit_id );
		$this->remote_location_data = new ISSSLPG_Remote_Data();
	}

	public function download_tables() {
		if ( $this->queue->is_complete() ) {
			return true;
		}
		$this->queue->maybe_create_queue_items();
		$response = $this->download_pending_table_part();
		if ( is_null( $response ) ) {
			return null;
		}
		return $this->queue->is_complete();
	}

	/**
	 * @return true When table is completely loaded.
	 * @return false When table is not completely loaded.
	 */
	private function download_pending_table_part( $limit = 5000 ) {
		$table_name = $this->queue->get_pending_table_name();
		$unit_category = $this->queue->get_pending_unit_category();
		$queue_item = $this->queue->get_current_item();
		// Return true if item is already loaded
		if ( $queue_item->is_complete() ) {
			return true;
		}

		$item_count = $offset = $queue_item->get_item_count();
		$remote_items = $this->remote_location_data->load_table_part( $unit_category, $this->unit_id, $table_name, $offset, $limit );
		if ( ! $remote_items ) {
			return null;
		}

		// Seeding
		$seeding_successful = call_user_func_array(
			array( $this->seeder, "seed_{$table_name}" ),
			array( $remote_items )
		);

		if ( $seeding_successful ) {
			$remote_item_count = count( $remote_items );
			$current_item_count = $item_count + $remote_item_count;
			$queue_item->update_item( $current_item_count );
			return $queue_item->is_complete();
		}

		return null;
	}

	public function seed_queue() {
		$this->queue->maybe_create_queue_items();
	}

	public function get_progress() {
		return $this->queue->get_progress();
	}

}