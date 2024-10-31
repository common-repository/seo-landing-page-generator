<?php



class ISSSLPG_Admin_Local_Content_Post_Type_Registration {

	public function __construct() {
		$this->register_post_type();
	}

	public function register_post_type() {

		$slug = _x( 'lc', 'URL slug (no spaces or special characters)', 'issslpg' );

		$labels = apply_filters( 'issslpg_local_content_labels', array(
			'name'               => _x( 'Local Content', 'post type name', 'issslpg' ),
			'singular_name'      => _x( 'Local Content', 'singular post type name', 'issslpg' ),
			'add_new'            => _x( 'Add New', 'landing page', 'issslpg' ),
			'add_new_item'       => __( 'Add New Local Content', 'issslpg' ),
			'edit_item'          => __( 'Edit Local Content', 'issslpg' ),
			'new_item'           => __( 'New Local Content', 'issslpg' ),
			'view_item'          => __( 'View Local Content', 'issslpg' ),
			'search_items'       => __( 'Search Local Content', 'issslpg' ),
			'not_found'          => __( 'No Local Content found', 'issslpg' ),
			'not_found_in_trash' => __( 'No Local Content found in trash', 'issslpg' ),
			'parent_item_colon'  => '',
		) );

		$args = apply_filters( 'issslpg_local_content_args', array(
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

		register_post_type( 'issslpg-local', $args );
	}

}