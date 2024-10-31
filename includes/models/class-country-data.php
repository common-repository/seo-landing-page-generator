<?php

namespace WeDevs\ORM\WP;

use WeDevs\ORM\Eloquent\Model;

class CountryData extends Model
{
	protected $table;

	protected $table_name_prefix;

	protected $fillable = [ 'id', 'active', 'phone' ];

	public $timestamps = false;

	public function __construct( array $attributes = array() ) {
		parent::__construct( $attributes );
		global $wpdb;
		$this->table_name_prefix = $wpdb->prefix;
		$this->table = "{$this->table_name_prefix}issslpg_country_data";
	}

	public function country() {
		return $this->belongsTo('WeDevs\ORM\WP\Country');
	}

}