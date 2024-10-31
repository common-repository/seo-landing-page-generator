<?php

namespace WeDevs\ORM\WP;

use WeDevs\ORM\Eloquent\Model;

class StateDemographics extends Model
{
	protected $primaryKey = 'state_id';

	protected $table;

	protected $table_name_prefix;

	protected $fillable = [ 'state_id', 'population', 'households', 'median_income', 'land_area', 'water_area', 'fbi_data', 'population_data', 'education_data' ];

	public $timestamps = false;

	public function __construct( array $attributes = array() ) {
		parent::__construct( $attributes );
		global $wpdb;
		$this->table_name_prefix = $wpdb->prefix;
		$this->table = "{$this->table_name_prefix}issslpg_state_demographics";
	}

	public function state() {
		return $this->belongsTo( 'WeDevs\ORM\WP\State' );
	}

}