<?php



class ISSSLPG_Admin_Template_Page_Post_Type_Registration {

	public function __construct() {
		$this->register_post_type();
		$this->register_taxonomy();
	}

	public function register_post_type() {

		$slug = _x( 'tp', 'URL slug (no spaces or special characters)', 'issslpg' );

		$labels = apply_filters( 'issslpg_template_page_labels', array(
			'name'               => _x( 'Template Pages', 'post type name', 'issslpg' ),
			'singular_name'      => _x( 'Template Page', 'singular post type name', 'issslpg' ),
			'add_new'            => _x( 'Add New', 'template page', 'issslpg' ),
			'add_new_item'       => __( 'Add New Template Page', 'issslpg' ),
			'edit_item'          => __( 'Edit Template Page', 'issslpg' ),
			'new_item'           => __( 'New Template Page', 'issslpg' ),
			'view_item'          => __( 'View Template Page', 'issslpg' ),
			'search_items'       => __( 'Search Template Pages', 'issslpg' ),
			'not_found'          => __( 'No Template Page found', 'issslpg' ),
			'not_found_in_trash' => __( 'No Template Pages found in trash', 'issslpg' ),
			'parent_item_colon'  => '',
		) );

		$args = apply_filters( 'issslpg_template_page_args', array(
			'labels'              => $labels,
			'public'              => true,
			'show_in_nav_menus'   => false,
			'exclude_from_search' => false,
			'capability_type'     => 'page',
			'supports'            => array(
				'title',
				'editor',
//				'post-formats',
				'thumbnail',
//				'revisions',
				'excerpt',
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

		register_post_type( 'issslpg-template', $args );
	}

	public function register_taxonomy() {
		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => _x( 'Categories', 'taxonomy general name', 'issslpg' ),
			'singular_name'     => _x( 'Category', 'taxonomy singular name', 'issslpg' ),
			'search_items'      => __( 'Search Categories', 'issslpg' ),
			'all_items'         => __( 'All Categories', 'issslpg' ),
			'parent_item'       => __( 'Parent Category', 'issslpg' ),
			'parent_item_colon' => __( 'Parent Category:', 'issslpg' ),
			'edit_item'         => __( 'Edit Category', 'issslpg' ),
			'update_item'       => __( 'Update Category', 'issslpg' ),
			'add_new_item'      => __( 'Add New Category', 'issslpg' ),
			'new_item_name'     => __( 'New Category Name', 'issslpg' ),
			'menu_name'         => __( 'Category', 'issslpg' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'service-category' ),
		);

		register_taxonomy( 'issslpg-template-category', array( 'issslpg-template' ), $args );
	}

}