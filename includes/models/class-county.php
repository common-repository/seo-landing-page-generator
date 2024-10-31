<?php

namespace WeDevs\ORM\WP;

use WeDevs\ORM\Eloquent\Model;

class County extends Model
{
	protected $table;

	protected $table_name_prefix;

	public $timestamps = false;

	public function __construct( array $attributes = array() ) {
		parent::__construct( $attributes );
		global $wpdb;
		$this->table_name_prefix = $wpdb->prefix;
		$this->table = "{$this->table_name_prefix}issslpg_counties";
	}

	public function countyData() {
		return $this->hasOne('WeDevs\ORM\WP\CountyData');
	}

	public function state() {
		return $this->belongsTo('WeDevs\ORM\WP\State');
	}

	public function cities() {
		return $this->belongsToMany('WeDevs\ORM\WP\City', "{$this->table_name_prefix}issslpg_city_county");
	}

	public function demographics() {
		return $this->hasOne('WeDevs\ORM\WP\CountyDemographics');
	}
}