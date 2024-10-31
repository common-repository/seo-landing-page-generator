<?php

namespace WeDevs\ORM\WP;

use WeDevs\ORM\Eloquent\Model;

class CountyData extends Model
{
	protected $table;

	protected $table_name_prefix;

	protected $fillable = [ 'id', 'active', 'phone', 'custom_locations', 'office_google_pid', 'county_id', 'settings' ];

	public $timestamps = false;

	public function __construct( array $attributes = array() ) {
		parent::__construct( $attributes );
		global $wpdb;
		$this->table_name_prefix = $wpdb->prefix;
		$this->table = "{$this->table_name_prefix}issslpg_county_data";
	}

	public function county() {
		return $this->belongsTo('WeDevs\ORM\WP\County');
	}

}