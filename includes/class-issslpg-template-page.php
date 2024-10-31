<?php



class ISSSLPG_Template_Page {

	static public function is_template_page( $page_id = false ) {
		$page_id = $page_id ? $page_id : get_the_ID();
		return ( 'issslpg-template' == get_post_type( $page_id ) );
	}

	static public function create_or_update_corralating_landing_pages( $page_id ) {
		if ( self::is_template_page( $page_id ) ) {
			ISSSLPG_Landing_Page::create_or_update_correlating_landing_pages_by_template_page( $page_id );
		}
	}

	static public function delete_corralating_landing_pages( $page_id ) {
		if ( self::is_template_page( $page_id ) ) {
			ISSSLPG_Landing_Page::delete_correlating_landing_pages_by_template_page( $page_id );
		}
	}

	static public function maybe_delete_corralating_landing_pages( $page_id ) {
		$template_pages = new WP_Query( array(
			'post_type'      => 'issslpg-template',
			'post_status'    => 'trash',
			'posts_per_page' => 1,
			'post__in'       => array( $page_id ),
		) );
		if ( $template_pages->have_posts() ) {
			$template_pages->the_post();
			// Delete Landing Pages if toggler is set to 'off'
//			if ( empty( get_post_meta( $page_id, '_issslpg_landing_page_toggler', true ) ) ) {
				ISSSLPG_Landing_Page::delete_correlating_landing_pages_by_template_page( $page_id );
//			}
		}
		wp_reset_postdata();
	}

	static public function has_published_corralating_landing_pages( $page_id ) {
		if ( ! self::is_template_page( $page_id ) ) {
			return false;
		}

		$landing_pages = new WP_Query( array(
			'post_type'      => 'issslpg-landing-page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'meta_query'  => array(
				array(
					'key'     => '_issslpg_template_page_id',
					'value'   => (string) $page_id,
					'compare' => '='
				),
			),
		) );

		if ( $landing_pages->have_posts() ) {
			wp_reset_postdata();
			return true;
		}

		wp_reset_postdata();
		return false;
	}

}