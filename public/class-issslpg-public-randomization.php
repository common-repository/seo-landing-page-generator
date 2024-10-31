<?php



class ISSSLPG_Public_Randomization {

	public function __construct() {}

	public function get_line_from_text( $text, $line_id ) {
		$text = $this->get_lines_from_text( $text );

		if ( ! isset( $text[$line_id] ) ) {
			return;
		}

		return $text[$line_id];
	}

	protected function get_lines_from_text( $text ) {
		$line_array = explode( "\n", $text );

		if ( ! is_array( $line_array ) ) {
			return array( $text );
		}

		// Clean out empty lines
		foreach ( $line_array as $key => $line ) {
			$line = trim( $line );
			if ( empty( $line ) ) {
				unset( $line_array[$key] );
			} else {
				// Sanitize line
				$replace = array( "\n", "\r", "<br>", "<br />", "<br/>" );
				$line_array[$key] = str_replace( $replace, "", $line );
			}
		}

		return ISSSLPG_Helpers::reduce_array_by_keyword_limit( $line_array );
	}

	public function get_random_line_id_from_text( $meta_group_id, $text ) {

		$line_array = self::get_lines_from_text( $text );

		if ( empty( $line_array ) ) {
			return;
		}

		$line_count = count( $line_array );
		$random_line_id = rand( 0, $line_count - 1 );

		// Get random line
//		shuffle( $line_array );
//		$random_line = $line_array[0];

		return $random_line_id;
	}

	public function get_random_line_id_from_text_without_duplicates( $meta_group_id, $text ) {

		$used_line_ids = array();

		if ( empty( $used_line_ids[ $meta_group_id ] ) ) {
			$used_line_ids[ $meta_group_id ] = array();
		}
		$used_line_ids_group = &$used_line_ids[ $meta_group_id ];

		$line_array = self::get_lines_from_text( $text );

		if ( ! empty( $line_array ) ) {
			$used_line_count = count( $used_line_ids_group );
			$line_count = count( $line_array );

			// Lets grab a random line and make sure we haven't used it prior.
			while ( $used_line_count < $line_count ) {
				$random_line_id = rand( 0, $line_count - 1 );
//				shuffle($line_array);
//				$random_line = $line_array[0];
				if ( ! in_array( $random_line_id, $used_line_ids_group ) ) {
					$used_line_ids_group[] = $random_line_id;
					return $random_line_id;
				}
			}
		}

		return;
	}

	public function get_processed_random_content( $meta_group_id, $meta_field_id, $content = '' ) {
		$content = self::get_random_content( $meta_group_id, $meta_field_id, $content );
		return ISSSLPG_Meta_Data::process_content( $content );
	}

	public function get_random_content_id( $meta_group_id, $meta_field_id, $post_id = false ) {

		$content_blocks = ISSSLPG_Meta_Data::get_group_fields( $meta_group_id, $meta_field_id, $post_id );

		if ( ! empty( $content_blocks ) ) {
			$content_blocks      = ISSSLPG_Helpers::reduce_array_by_rows_limit( $content_blocks );
			$content_block_count = count( $content_blocks );
			$random_content_id   = rand( 1, $content_block_count );
			return $random_content_id;
		}

		return null;
	}

	public function get_random_content( $meta_group_id, $meta_field_id, $content = '' ) {

		$content_blocks = ISSSCR_Meta_Data::get_group_fields( $meta_group_id, $meta_field_id );

		if ( ! empty( $content_blocks ) ) {
			$content_blocks = ISSSCR_Helpers::reduce_array_by_rows_limit( $content_blocks );
			shuffle( $content_blocks );
			$content = $content_blocks[0];
		}

		return $content;
	}

	public function get_random_image_id( $meta_group_id, $meta_field_id, $post_id = false ) {
		$image_ids = ISSSLPG_Meta_Data::get_group_fields( $meta_group_id, $meta_field_id, $post_id );

		if ( empty( $image_ids ) ) {
			return;
		}

		$image_ids = ISSSLPG_Helpers::reduce_array_by_rows_limit( $image_ids, 2 );

		shuffle( $image_ids );
		$random_image_id = $image_ids[0];

		return $random_image_id;
//		return wp_get_attachment_image( $random_image_id, $image_size, false, array( 'class' => $class ) );
	}

	public function get_random_image_id_without_duplicates( $meta_group_id, $meta_field_id, $post_id = false ) {

		static $used_image_ids = array();

		if ( empty( $used_image_ids[ $meta_group_id ] ) ) {
			$used_image_ids[ $meta_group_id ] = array();
		}
		$used_image_group_ids = &$used_image_ids[ $meta_group_id ];

		$image_ids = ISSSLPG_Meta_Data::get_group_fields( $meta_group_id, $meta_field_id, $post_id );

		if ( empty( $image_ids ) ) {
			return;
		}

		$image_ids         = ISSSLPG_Helpers::reduce_array_by_rows_limit( $image_ids, 2 );
		$image_count       = count( $image_ids );
		$used_images_count = count( $used_image_group_ids );
		$image_id          = false;

		// Lets grab a random image and make sure we haven't used it prior.
		while ( $used_images_count < $image_count ) {
			shuffle( $image_ids );
			$random_image_id = $image_ids[0];
			// If we don't see the randomly selected image in the used
			// images array, add it to the the array and break the loop,
			// as we found a unique random image.
			if ( ! in_array( $random_image_id, $used_image_group_ids ) ) {
				$image_id               = $random_image_id;
				$used_image_group_ids[] = $random_image_id;
				break;
			}
		}

		return $image_id;
//		return wp_get_attachment_image( $image_id, $image_size, false, array( 'class' => $class ) );
	}

}