<?php



class ISSSLPG_Logger {

	static public function log( $message, $method = __METHOD__, $category = 'note' ) {
		if ( ISSSLPG_Options::get_setting( 'active_logger', false, 'iss_debug_settings' ) || ( defined( 'ISSSLPG_LOG' ) && ISSSLPG_LOG ) ) {
			global $wpdb;
			if ( ISSSLPG_Options::get_setting( 'remove_duplicate_logs', true, 'iss_debug_settings' ) ) {
				// Don't log message if same message has just been logged
				$last_message = $wpdb->get_var( "SELECT `message` FROM {$wpdb->prefix}issslpg_logs ORDER BY `id` ASC LIMIT 1" );
				$last_method = $wpdb->get_var( "SELECT `method` FROM {$wpdb->prefix}issslpg_logs ORDER BY `id` ASC LIMIT 1" );
				if ( $message == $last_message && $method == $last_method ) {
					return false;
				}
			}
			$wpdb->insert( "{$wpdb->prefix}issslpg_logs",
				[
					'category' => $category,
					'method' => $method,
					'message' => $message,
				],
				[ '%s', '%s', '%s' ]
			);
		}
	}

	static public function get_logs() {
		global $wpdb;
		return $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}issslpg_logs` ORDER BY `id` DESC LIMIT 3000", ARRAY_A );
	}

	static public function get_logs_as_text() {
		$logs = self::get_logs();
		$text = '';
		foreach ( $logs as $entry ) {
//			$category = strtoupper( $entry['category'] );
			$text.= "[{$entry['datetime']}] {$entry['message']} (in {$entry['method']})\r";
		}
		return $text;
	}

	static public function delete_overflow() {
		global $wpdb;
		$wpdb->query( "DELETE FROM `{$wpdb->prefix}issslpg_logs` WHERE `datetime` < NOW() - INTERVAL 2 DAY" );
	}

}