<?php

class ISSSLPG_Admin_CMB2_Plugin_Custom_Fields_Registration {

	public function __construct() {
		$this->register_custom_fields();
	}

	private function register_custom_fields()
	{
		add_action( 'cmb2_render_notification', array( $this, 'render_notification' ), 10, 5 );
		add_action( 'cmb2_render_sitemap_export', array( $this, 'render_sitemap_export' ), 10, 5 );
		add_action( 'cmb2_render_button', array( $this, 'render_button' ), 10, 5 );
		add_action( 'cmb2_render_template_page_sort_list', array( $this, 'render_template_page_sort_list' ), 10, 5 );
//		add_action( 'cmb2_render_business_day_hours', array( $this, 'render_business_day_hours' ), 10, 5 );
	}

//	public function render_business_day_hours( $field, $value, $object_id, $object_type, $field_type_object ) {
//		$value = wp_parse_args( $value, array(
//			'active' => '',
//			'open'   => '',
//			'close'  => '',
//		) );
//
//		echo $field_type_object->input( array(
//			'id'    => $field->id( '_active' ),
//			'name'  => $field->name( '[active]' ),
//			'value' => $value['active'],
//			'type'  => 'checkbox',
//			'class' => 'cmb2-option cmb2-list',
//		) );
//
//		echo $field_type_object->input( array(
//			'id'    => $field->id( '_open' ),
//			'name'  => $field->name( '[open]' ),
//			'value' => $value['open'],
//			'type'  => 'text',
//			'class' => 'cmb2-text-small',
//				'placeholder' => '09:00',
//		) );
//		echo ' <span>&ndash;</span> ';
//		echo $field_type_object->input( array(
//			'id'    => $field->id( '_close' ),
//			'name'  => $field->name( '[close]' ),
//			'value' => $value['close'],
//			'type'  => 'text',
//			'class' => 'cmb2-text-small',
//				'placeholder' => '17:00',
//		) );
//	}

	public function render_notification( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		if ( empty( $field->args['note'] ) ) {
			return;
		}

		echo $field->args['note'];
	}

	public function render_sitemap_export( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		$template_pages = get_posts( array(
			'post_type'      => 'issslpg-template',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		) );

		if ( ! $template_pages ) {
			return;
		}

		echo '<div class="issslpg-cmb2-html-sitemap-export-field">';
		echo '<select class="js-issslpg-html-sitemap-export-select">';
		foreach ( $template_pages as $template_page ) {
			$title = get_the_title( $template_page );
			echo "<option value='{$template_page->ID}'>{$title}</option>";
		}
		echo '</select>';
		echo '&nbsp;';

		$base_url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
		$url = $base_url . $_SERVER['REQUEST_URI'];
		$href = $url . '&export_html_sitemap_csv=true' . "&export_html_sitemap_template={$template_pages[0]->ID}";
		echo "<a href='{$href}' class='button  button-primary  js-issslpg-html-sitemap-button  issslpg-html-sitemap-button'>";
		echo     __( 'Export Sitemap', 'issslpg' );
		echo '</a>';
		echo '</div>';

		if ( isset( $field->args['desc'] ) && ! empty( $field->args['desc'] ) ) {
			echo '<p class="cmb2-metabox-description">';
			echo     $field->args['desc'];
			echo '</p>';
		}
	}

	public function render_button( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		if ( ! isset( $field->args['href'] ) || ! isset( $field->args['button_title'] ) ) {
			return;
		}
		if ( empty( $field->args['href'] ) || empty( $field->args['button_title'] ) ) {
			return;
		}

		echo "<a href='{$field->args['href']}' class='button button-primary'>";
		echo     $field->args['button_title'];
		echo '</a>';

		if ( isset( $field->args['desc'] ) && ! empty( $field->args['desc'] ) ) {
			echo '<p class="cmb2-metabox-description">';
			echo     $field->args['desc'];
			echo '</p>';
		}
	}

	public function render_template_page_sort_list( $field, $escaped_value, $object_id, $object_type, $field_type ) {
		$template_pages = get_posts( array(
			'post_type'      => 'issslpg-template',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		) );

		if ( ! $template_pages ) {
			return;
		}

		$text_field_value_array = [];

		echo '<ul id="issslpgSortList" class="issslpg-sort-list">';
		foreach ( $template_pages as $template_page ) {
			$title = get_the_title( $template_page );
			$text_field_value_array[]= $template_page->ID;
			echo "<li class='issslpg-sort-list-item' data-id='{$template_page->ID}'>";
			echo "<span class='issslpg-sort-list-item-icon dashicons dashicons-menu'></span>";
			echo "<span class='issslpg-sort-list-item-title'>{$title}</span>";
			echo "</li>";
		}

		echo '</ul>';

		$text_field_value = join( ',', $text_field_value_array);
		echo $field_type->input( array(
			'class' => 'cmb_text issslpg-sort-list-values issslpg-hidden',
			'name'  => $field_type->_name(),
			'id'    => $field_type->_id(),
			'value' => $text_field_value,
			'type'  => 'text',
		) );

//		if ( isset( $field->args['desc'] ) && ! empty( $field->args['desc'] ) ) {
//			echo '<p class="cmb2-metabox-description">';
//			echo     $field->args['desc'];
//			echo '</p>';
//		}

		echo "
			<script>
				Sortable.create(issslpgSortList, {
					group: 'issslpgSortList',
					store: {
						/**
						 * Get the order of elements. Called once during initialization.
						 * @param   {Sortable}  sortable
						 * @returns {Array}
						 */
						get: function (sortable) {
							var order = localStorage.getItem(sortable.options.group.name);
							console.log(order);
							document.getElementsByName('template_page_priority')[0].value = order;
							return order ? order.split(',') : [];
						},

						/**
						 * Save the order of elements. Called onEnd (when the item is dropped).
						 * @param {Sortable}  sortable
						 */
						set: function (sortable) {
							var order = sortable.toArray();
							console.log(order);
							document.getElementsByName('template_page_priority')[0].value = order.join(',');
							localStorage.setItem(sortable.options.group.name, order.join(','));
						}
					}
				});
			</script>
		";
	}

}