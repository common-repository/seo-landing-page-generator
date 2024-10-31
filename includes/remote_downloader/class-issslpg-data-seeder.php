<?php

class ISSSLPG_Data_Seeder {

	private $wpdb;

	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	public function seed_counties( $rows ) {
		$query = "REPLACE INTO {$this->wpdb->prefix}issslpg_counties (`id`, `name`, `state_id`) VALUES ";
		foreach ( $rows as $row ) {
			$query.= "({$row->id},\"{$row->name}\",{$row->state_id}),";
		}
		$query = substr( $query, 0, -1 );
		$query.= ';';

		return $this->wpdb->query( $query );
	}

	public function seed_cities( $rows ) {
		$query = "REPLACE INTO {$this->wpdb->prefix}issslpg_cities (`id`, `name`, `state_id`, `country_id`) VALUES ";
		foreach ( $rows as $row ) {
			$query.= "({$row->id},\"{$row->name}\",{$row->state_id},{$row->country_id}),";
		}
		$query = substr( $query, 0, -1 );
		$query.= ';';

		return $this->wpdb->query( $query );
	}

	public function seed_city_county( $rows ) {
		$query = "REPLACE INTO {$this->wpdb->prefix}issslpg_city_county (`id`, `city_id`, `county_id`) VALUES ";
		foreach ( $rows as $row ) {
			$query.= "({$row->id},{$row->city_id},{$row->county_id}),";
		}
		$query = substr( $query, 0, -1 );
		$query.= ';';

		return $this->wpdb->query( $query );
	}

	public function seed_city_zip_code( $rows ) {
		$query = "REPLACE INTO {$this->wpdb->prefix}issslpg_city_zip_code (`id`, `city_id`, `zip_code_id`) VALUES ";
		foreach ( $rows as $row ) {
			$query.= "({$row->id},{$row->city_id},{$row->zip_code_id}),";
		}
		$query = substr( $query, 0, -1 );
		$query.= ';';

		return $this->wpdb->query( $query );
	}

	public function seed_zip_codes( $rows ) {
		$query = "REPLACE INTO {$this->wpdb->prefix}issslpg_zip_codes (`id`, `zip_code`) VALUES ";
		foreach ( $rows as $row ) {
			$query.= "({$row->id},\"{$row->zip_code}\"),";
		}
		$query = substr( $query, 0, -1 );
		$query.= ';';

		return $this->wpdb->query( $query );
	}

	public function seed_city_demographics( $rows ) {
		$query = "REPLACE INTO {$this->wpdb->prefix}issslpg_city_demographics (`city_id`, `state_id`, `type`, `geo_id`, `population`, `households`, `median_income`, `land_area`, `water_area`, `latitude`, `longitude`, `climate_data`, `fbi_data`) VALUES ";
		foreach ( $rows as $row ) {
			$query.= "({$row->city_id},{$row->state_id},'{$row->type}','{$row->geo_id}',{$row->population},{$row->households},{$row->median_income},{$row->land_area},{$row->water_area},{$row->latitude},{$row->longitude},'{$row->climate_data}','{$row->fbi_data}'),";
		}
		$query = substr( $query, 0, -1 );
		$query.= ';';

		return $this->wpdb->query( $query );
	}

	public function seed_county_demographics( $rows ) {
		$query = "REPLACE INTO {$this->wpdb->prefix}issslpg_county_demographics (`state_id`, `county_id`, `population`, `households`, `median_income`, `land_area`, `water_area`, `climate_data`, `fbi_data`) VALUES ";
		foreach ( $rows as $row ) {
			$query.= "({$row->state_id},{$row->county_id},{$row->population},{$row->households},{$row->median_income},{$row->land_area},{$row->water_area},'{$row->climate_data}','{$row->fbi_data}'),";
		}
		$query = substr( $query, 0, -1 );
		$query.= ';';

		return $this->wpdb->query( $query );
	}

	public function seed_state_demographics( $rows ) {
		$query = "REPLACE INTO {$this->wpdb->prefix}issslpg_state_demographics (`state_id`, `population`, `households`, `median_income`, `land_area`, `water_area`, `fbi_data`, `population_data`, `education_data`) VALUES ";
		foreach ( $rows as $row ) {
			$query.= "({$row->state_id},'{$row->population}',{$row->households},{$row->median_income},{$row->land_area},{$row->water_area},'{$row->fbi_data}','{$row->population_data}','{$row->education_data}'),";
		}
		$query = substr( $query, 0, -1 );
		$query.= ';';

		return $this->wpdb->query( $query );
	}

}