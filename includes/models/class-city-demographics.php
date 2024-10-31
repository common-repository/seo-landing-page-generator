<?php

namespace WeDevs\ORM\WP;

use WeDevs\ORM\Eloquent\Model;

class CityDemographics extends Model
{
	protected $primaryKey = 'city_id';

	protected $table;

	protected $table_name_prefix;

	protected $fillable = [ 'city_id', 'state_id', 'type', 'geo_id', 'population', 'households', 'median_income', 'land_area', 'water_area', 'latitude', 'longitude', 'climate_data', 'fbi_data' ];

	public $timestamps = false;

	public function __construct( array $attributes = array() ) {
		parent::__construct( $attributes );
		global $wpdb;
		$this->table_name_prefix = $wpdb->prefix;
		$this->table = "{$this->table_name_prefix}issslpg_city_demographics";
	}

	public function city() {
		return $this->belongsTo('WeDevs\ORM\WP\City');
	}

	public function state() {
		return $this->belongsTo('WeDevs\ORM\WP\State');
	}

}