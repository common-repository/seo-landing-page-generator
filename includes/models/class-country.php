<?php

namespace WeDevs\ORM\WP;

use WeDevs\ORM\Eloquent\Model;

class Country extends Model
{
	protected $table;

	protected $table_name_prefix;

	public $timestamps = false;

	public function __construct( array $attributes = array() ) {
		parent::__construct( $attributes );
		global $wpdb;
		$this->table_name_prefix = $wpdb->prefix;
		$this->table = "{$this->table_name_prefix}issslpg_countries";
	}

	public function countryData() {
		return $this->hasOne('WeDevs\ORM\WP\CountryData');
	}

	public function states() {
		return $this->hasMany('WeDevs\ORM\WP\State')->orderBy('name');
	}

	public function counties() {
		return $this->hasMany('WeDevs\ORM\WP\County')->orderBy('name');
	}

	public function cities() {
		return $this->hasMany('WeDevs\ORM\WP\City')->orderBy('name');
	}
}