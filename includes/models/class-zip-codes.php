<?php

namespace WeDevs\ORM\WP;

use WeDevs\ORM\Eloquent\Model;

class ZipCode extends Model
{
	protected $table;

	protected $table_name_prefix;

	public $timestamps = false;

	public function __construct( array $attributes = array() ) {
		parent::__construct( $attributes );
		global $wpdb;
		$this->table_name_prefix = $wpdb->prefix;
		$this->table = "{$this->table_name_prefix}issslpg_zip_codes";
	}

	public function cities() {
		return $this->belongsToMany('WeDevs\ORM\WP\City', "{$this->table_name_prefix}issslpg_city_zip_code")->orderBy('name');
	}
}