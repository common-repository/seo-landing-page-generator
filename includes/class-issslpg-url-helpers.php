<?php



class ISSSLPG_Url_Helpers {

	static function title_to_slug( $title, $context = 'display' ) {
		$title = sanitize_title_with_dashes( $title, false, $context );
		return $title;
	}

	static function title_to_query( $title ) {
		$title = sanitize_title_for_query( $title );
		$title = str_replace( '-', ' ', $title );
		return $title;
	}

}