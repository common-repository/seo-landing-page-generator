<?php

namespace WeDevs\ORM\WP;

use WeDevs\ORM\Eloquent\Model;

class StateData extends Model
{
	protected $table;

	protected $table_name_prefix;

	protected $fillable = [ 'id', 'active', 'phone', 'office_google_pid', 'state_id' ];

	public $timestamps = false;

	public function __construct( array $attributes = array() ) {
		parent::__construct( $attributes );
		global $wpdb;
		$this->table_name_prefix = $wpdb->prefix;
		$this->table = "{$this->table_name_prefix}issslpg_state_data";
	}

	public function state() {
		return $this->belongsTo('WeDevs\ORM\WP\State');
	}

}