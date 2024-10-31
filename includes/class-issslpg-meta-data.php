<?php



class ISSSLPG_Meta_Data {

	public static function get_processed_content( $group_id, $field_id, $entry_number, $post_id = false ) {
		$content = ISSSLPG_Meta_Data::get_group_field( $group_id, $field_id, $entry_number, $post_id );
		return self::process_content( $content );
	}

	public static function get_group_field( $group_id, $field_id, $entry_number, $post_id = false ) {
		$group_fields = self::get_group_fields( $group_id, $field_id, $post_id );

		$entry_number = (int) $entry_number;
		$entry_number--;
		if ( isset( $group_fields[$entry_number] ) ) {
			$field = $group_fields[$entry_number];
			return $field;
		}

		return false;
	}

	public static function get_group_fields( $group_id, $field_id, $post_id = false ) {

		$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
		$field_array = array();

		$group = get_post_meta( $post_id, $group_id, true );

		if ( ! empty( $group ) && is_array( $group ) ) {
			foreach( (array)$group as $key => $field ) {
				$field_array[] = $field[$field_id];
			}
		}

		return $field_array;
	}

	public static function process_content( $content ) {

		if ( ! empty( $content ) ) {
			global $wp_embed;
			$content = $wp_embed->autoembed( $content );
			$content = $wp_embed->run_shortcode( $content );
			$content = wpautop( $content );
			$content = do_shortcode( $content );
			$content = preg_replace( '#<p>\s*</p>#', '', $content ); // Remove empty p tags
		}

		return $content;
	}

}