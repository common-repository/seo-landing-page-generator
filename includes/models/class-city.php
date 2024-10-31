<?php

namespace WeDevs\ORM\WP;

use WeDevs\ORM\Eloquent\Model;

class City extends Model
{
	protected $table;

	protected $table_name_prefix;

	public $timestamps = false;

	public function __construct( array $attributes = array() ) {
		parent::__construct( $attributes );
		global $wpdb;
		$this->table_name_prefix = $wpdb->prefix;
		$this->table = "{$this->table_name_prefix}issslpg_cities";
	}

	public function cityData() {
		return $this->hasOne('WeDevs\ORM\WP\CityData');
	}

	public function country() {
		return $this->belongsTo('WeDevs\ORM\WP\Country');
	}

	public function state() {
		return $this->belongsTo('WeDevs\ORM\WP\State');
	}

	public function counties() {
		return $this->belongsToMany('WeDevs\ORM\WP\County', "{$this->table_name_prefix}issslpg_city_county")->orderBy('name');
	}

	public function zipCodes() {
		return $this->belongsToMany('WeDevs\ORM\WP\ZipCode', "{$this->table_name_prefix}issslpg_city_zip_code");
	}

	public function demographics() {
		return $this->hasOne('WeDevs\ORM\WP\CityDemographics');
	}

}