<?php



class ISSSLPG_Admin_CSV_Helper {

	static public function array_to_csv( $array ) {
		$csv_array = array();
		$csv_header_array = array();

		foreach( $array as $array_element ) {
			$csv_row_array = array();
			foreach( $array_element as $key => $value ) {
				// Header
				if ( empty( $csv_array ) ) {
					$csv_header_array[] = "\"{$key}\"";
				}
				// Body
				if ( empty( $value ) ) {
					$csv_row_array[] = "\"\"";
				}
				elseif ( is_array( $value ) ) {
					$joined = join( ',', $value );
					$csv_row_array[] = "\"{$joined}\"";
				}
				else {
					$csv_row_array[]= "\"{$value}\"";
				}
			}
			$csv_array[]= join( ',', $csv_row_array );
		}

		$output = '';
		$output.= join( ",", $csv_header_array );
		$output.= "\n";
		$output.= join( "\n", $csv_array );
		return $output;
	}

}