<?php

namespace WeDevs\ORM\WP;

use WeDevs\ORM\Eloquent\Model;

class State extends Model
{
	protected $table;

	protected $table_name_prefix;

	public $timestamps = false;

	public function __construct( array $attributes = array() ) {
		parent::__construct( $attributes );
		global $wpdb;
		$this->table_name_prefix = $wpdb->prefix;
		$this->table = "{$this->table_name_prefix}issslpg_states";
	}

	public function stateData() {
		return $this->hasOne('WeDevs\ORM\WP\StateData');
	}

	public function country() {
		return $this->belongsTo('WeDevs\ORM\WP\Country');
	}

	public function counties() {
		return $this->hasMany('WeDevs\ORM\WP\County')->orderBy('name');
	}

	public function cities() {
		return $this->hasMany('WeDevs\ORM\WP\City')->orderBy('name');
	}

	public function demographics() {
		return $this->hasOne('WeDevs\ORM\WP\StateDemographics');
	}
}