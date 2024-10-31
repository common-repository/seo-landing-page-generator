<?php



class ISSSLPG_Admin_Landing_Page_Post_Type_Registration {

	public function __construct() {
		$this->register_post_type();
	}

	public function register_post_type() {

//		$slug = _x( 'lp', 'URL slug (no spaces or special characters)', 'issslpg' );
//		var_dump( ISSSLPG_Options::get_setting( 'landing_page_slug', 'lp' ));
		$slug = esc_html( ISSSLPG_Options::get_setting( 'landing_page_slug', 'lp' ) );

		$labels = apply_filters( 'issslpg_landing_page_labels', array(
			'name'               => _x( 'Landing Pages', 'post type name', 'issslpg' ),
			'singular_name'      => _x( 'Landing Page', 'singular post type name', 'issslpg' ),
			'add_new'            => _x( 'Add New', 'landing page', 'issslpg' ),
			'add_new_item'       => __( 'Add New Landing Page', 'issslpg' ),
			'edit_item'          => __( 'Edit Landing Page', 'issslpg' ),
			'new_item'           => __( 'New Landing Page', 'issslpg' ),
			'view_item'          => __( 'View Landing Page', 'issslpg' ),
			'search_items'       => __( 'Search Landing Pages', 'issslpg' ),
			'not_found'          => __( 'No Landing Page found', 'issslpg' ),
			'not_found_in_trash' => __( 'No Landing Pages found in trash', 'issslpg' ),
			'parent_item_colon'  => '',
		) );

		$args = apply_filters( 'issslpg_landing_page_args', array(
			'labels'              => $labels,
			'public'              => true,
			'show_in_nav_menus'   => false,
			'exclude_from_search' => false,
			'capability_type'     => 'page',
			'supports'            => array(
				'title',
//				'editor',
//				'post-formats',
//				'thumbnail',
//				'revisions',
//				'excerpt',
//				'comments',
//				'author',
//				'custom-fields',
			),
			'menu_position' => 5,
			'has_archive'   => false,
			'rewrite'       => array(
				'slug'       => $slug,
				'with_front' => false,
			),
		) );

		register_post_type( 'issslpg-landing-page', $args );
	}

}