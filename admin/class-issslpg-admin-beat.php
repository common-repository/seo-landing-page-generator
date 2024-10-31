<?php



class ISSSLPG_Admin_Beat {

	static public function maybe_set_next_beat() {
		$next_beat = get_option( 'issslpg_next_beat' );

		if ( ! $next_beat ) {
			self::set_next_beat();
		}

		if ( (int)$next_beat < time() ) {
			self::set_next_beat();
		}
	}

	static public function past_beat() {
		$next_beat = get_option( 'issslpg_next_beat' );
		return ( $next_beat && (int)$next_beat < time() );
	}

	static protected function set_next_beat() {
		$time = time() + 10;
		update_option( 'issslpg_next_beat', $time );
		ISSSLPG_Logger::log( 'Reset beat ('. $time .')', __METHOD__ );
	}

}