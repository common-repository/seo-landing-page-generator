<?php

namespace WeDevs\ORM\WP;

use WeDevs\ORM\Eloquent\Model;

class CountyDemographics extends Model
{
	protected $primaryKey = 'county_id';

	protected $table;

	protected $table_name_prefix;

	protected $fillable = [ 'county_id', 'state_id', 'population', 'households', 'median_income', 'land_area', 'water_area', 'climate_data', 'fbi_data' ];

	public $timestamps = false;

	public function __construct( array $attributes = array() ) {
		parent::__construct( $attributes );
		global $wpdb;
		$this->table_name_prefix = $wpdb->prefix;
		$this->table = "{$this->table_name_prefix}issslpg_county_demographics";
	}

	public function county() {
		return $this->belongsTo( 'WeDevs\ORM\WP\County' );
	}

}