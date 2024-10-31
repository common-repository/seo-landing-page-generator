<?php



class ISSSLPG_ISSSCR_Functions {

	static public function has_cache_manager() {
		return class_exists( 'ISSSCR_Cache_Manager' );
	}

}