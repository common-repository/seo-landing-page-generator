<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://intellasoftplugins.com
 *
 * @package    ISSSLPG
 * @subpackage ISSSLPG/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    ISSSLPG
 * @subpackage ISSSLPG/admin
 * @author     Ruven Pelka <ruven.pelka@gmail.com>
 */
class ISSSLPG_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->load_dependencies();
	}

	/**
	 * Load the required dependencies for this class.
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/plugins/cmb2/init.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/plugins/cmb2-switch-button/cmb2-switch-button.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/plugins/cmb2-grid-master/Cmb2GridPlugin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/class-issslpg-admin-cities-in-county-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/class-issslpg-admin-directions-map-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/class-issslpg-admin-map-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/class-issslpg-admin-related-landing-pages-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/class-issslpg-admin-zip-codes-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/class-issslpg-admin-state-demographics-population-data-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/class-issslpg-admin-state-demographics-education-data-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/class-issslpg-admin-state-demographics-crime-data-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/class-issslpg-admin-state-demographics-data-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/class-issslpg-admin-county-demographics-crime-data-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/class-issslpg-admin-county-demographics-climate-data-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/class-issslpg-admin-county-demographics-data-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/class-issslpg-admin-city-demographics-climate-data-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/class-issslpg-admin-city-demographics-crime-data-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/class-issslpg-admin-city-demographics-data-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issslpg-admin-cmb2-plugin-custom-fields-registration.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issslpg-admin-cmb2-plugin-render-business-hours-field.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issslpg-admin-cmb2-plugin-meta-box-registration.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issslpg-admin-cmb2-plugin-limited-meta-field-registration.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issslpg-admin-cmb2-plugin-settings-page-registration.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issslpg-admin-docs-page.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issslpg-admin-csv-helper.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issslpg-admin-location-settings-page.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issslpg-admin-landing-page-post-type-registration.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issslpg-admin-template-page-post-type-registration.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issslpg-admin-local-content-post-type-registration.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issslpg-admin-notices.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issslpg-admin-scheduled-tasks.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issslpg-admin-register-tinymce-shortcode-button.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issslpg-admin-beat.php';
	}

	/**
	 * Register Widgets.
	 *
	 * @wp-hook widgets_init
	 */
	public function register_widgets() {
		register_widget( 'ISSSLPG_Admin_Cities_In_County_Widget' );
		register_widget( 'ISSSLPG_Admin_Directions_Map_Widget' );
		register_widget( 'ISSSLPG_Admin_Map_Widget' );
		register_widget( 'ISSSLPG_Admin_Related_Landing_Pages_Widget' );
		register_widget( 'ISSSLPG_Admin_Zip_Codes_Widget' );
		if ( ISSSLPG_Helpers::is_demographics_usage_allowed() ) {
			register_widget( 'ISSSLPG_Admin_State_Demographics_Population_Data_Widget' );
			register_widget( 'ISSSLPG_Admin_State_Demographics_Education_Data_Widget' );
			register_widget( 'ISSSLPG_Admin_State_Demographics_Crime_Data_Widget' );
			register_widget( 'ISSSLPG_Admin_State_Demographics_Data_Widget' );
			register_widget( 'ISSSLPG_Admin_County_Demographics_Crime_Data_Widget' );
			register_widget( 'ISSSLPG_Admin_County_Demographics_Climate_Data_Widget' );
			register_widget( 'ISSSLPG_Admin_County_Demographics_Data_Widget' );
			register_widget( 'ISSSLPG_Admin_City_Demographics_Climate_Data_Widget' );
			register_widget( 'ISSSLPG_Admin_City_Demographics_Crime_Data_Widget' );
			register_widget( 'ISSSLPG_Admin_City_Demographics_Data_Widget' );
		}
	}


	/**
	 * Maybe Seed Download Queue with Demographics Slot.
	 *
	 * @wp-hook wp_ajax_update_download_queue
	 */
	public function update_download_queue() {
		if ( ! ISSSLPG_Helpers::is_demographics_usage_allowed() || ! wp_verify_nonce( $_POST['_nonce'], 'issslpg' ) ) {
			return false;
		}

		$add_slots = get_option( 'issslpg_seed_download_queue_with_demographics_slots' );
		if ( ! $add_slots || $add_slots == '0' ) {
			return false;
		}

		$pending_download_queue_items = get_option( 'issslpg_pending_download_queue_items' );
//		var_dump( $pending_download_queue_items);

		if ( ! $pending_download_queue_items || empty( $pending_download_queue_items ) ) {
			// Seed Download Queue for Demographics
			global $wpdb;
			$download_queue_table_name = "{$wpdb->prefix}issslpg_download_queue";
			$pending_unit_ids_results = $wpdb->get_results( "SELECT DISTINCT unit_id FROM {$download_queue_table_name} WHERE unit_category = 'locations'", ARRAY_N );
			$pending_download_queue_items = array();
			if ( $pending_unit_ids_results ) {
				foreach ( $pending_unit_ids_results as $pending_unit_id ) {
					$pending_download_queue_items[] = $pending_unit_id[0];
//					error_log( "ADDED UNIT ID: {$pending_unit_id[0]}" );
				}
			}
		}

		if ( isset( $pending_download_queue_items[0] ) ) {
			$remote_data_downloader = new ISSSLPG_Remote_Data_Downloader( array( 'demographics' ), $pending_download_queue_items[0] );
			$remote_data_downloader->seed_queue();
//			error_log( "SEEDED UNIT ID: {$pending_download_queue_items[0]}" );
			unset( $pending_download_queue_items[0] );
			if ( ! empty( $pending_download_queue_items ) ) {
				$pending_download_queue_items = array_values($pending_download_queue_items);
			}
		}

		update_option( 'issslpg_pending_download_queue_items', $pending_download_queue_items );

		if ( empty( $pending_download_queue_items ) ) {
			update_option( 'issslpg_seed_download_queue_with_demographics_slots', 0 );
		}
	}

	/**
	 * Admin menu functions.
	 *
	 * @wp-hook admin_menu
	 */
	public function admin_menu() {
		remove_menu_page('iss_debug_settings');
	}

	/**
	 * Add sample content.
	 *
	 * @wp-hook wp
	 */
	public function add_sample_content() {
		ISSSLPG_ISSSCR_Sample_Content::add_sample_service_template_page();
	}

	/**
	 * Localize AJAX Object.
	 *
	 * @wp-hook admin_enqueue_scripts
	 */
	public function localize_ajax_object( $hook ) {
		// if( 'index.php' != $hook ) {
		// 	// Only applies to dashboard panel
		// 	return;
		// }

		// Get pending unit IDs and categories
		global $wpdb;
		$download_queue_table_name = "{$wpdb->prefix}issslpg_download_queue";
		$results = $wpdb->get_results( "SELECT DISTINCT unit_id FROM {$download_queue_table_name} WHERE item_count != total_count" );
		$pending_unit_ids_array = array();
		foreach ( $results as $result ) {
			$pending_unit_ids_array[] = $result->unit_id;
		}
		$results = $wpdb->get_results( "SELECT DISTINCT unit_category FROM {$download_queue_table_name}" );
		$unit_categories_array = array();
		foreach ( $results as $result ) {
			$unit_categories_array[] = $result->unit_category;
		}

		wp_localize_script(
			'issslpg',
			'issslpg_ajax_object',
			array(
				'nonce' => wp_create_nonce( 'issslpg' ),
				'pending_unit_ids' => $pending_unit_ids_array,
				'pending_unit_categories' => join( ',', $unit_categories_array ),
			)
		);
	}

	/**
	 * AJAX callback to download remote units.
	 *
	 * @wp-hook wp_ajax_download_remote_unit
	 */
	function download_remote_unit() {
		global $wpdb; // this is how you get access to the database

		$status = 'error';
		$progress = 0;
		$unit_id = 0;

		if ( isset( $_POST['_nonce'] ) &&
			 isset( $_POST['unit_id'] ) &&
			 isset( $_POST['unit_categories'] ) &&
			 wp_verify_nonce( $_POST['_nonce'], 'issslpg' )
		) {
			$unit_id = intval( $_POST['unit_id'] );
			$unit_categories = explode( ',', $_POST['unit_categories'] );
//			$unit_categories = $_POST['unit_categories'];
//			error_log( $unit_categories);

			$loader = new ISSSLPG_Remote_Data_Downloader( $unit_categories, $unit_id );
			$unit_loaded = $loader->download_tables();
			if ( $unit_loaded ) {
				$progress = 100;
				$status = 'done';
			} elseif ( ! is_null( $unit_loaded ) ) {
				$progress = $loader->get_progress();
				$status = 'processing';
			}
		}

		$json = json_encode( array(
			'status'         => $status,
			'progress'       => $progress,
			'unit_id'        => $unit_id,
			// 'unit_categories'  => $unit_categories,
		) );
		echo $json;
		wp_die();
	}

	/**
	 * Add Cron Schedules.
	 *
	 * @wp-hook cron_schedules
	 */
	public function add_cron_schedules( $schedules ) {

		if ( ! isset( $schedules['everyminute'] ) ) {
			$schedules['everyminute'] = array(
				'interval' => 1 * 60,
				'display' => __( 'Every Minute', 'issslpg' )
			);
		}

		if ( ! isset( $schedules['everytenminutes'] ) ) {
			$schedules['everytenminutes'] = array(
				'interval' => 10 * 60,
				'display' => __( 'Every 10 Minutes', 'issslpg' )
			);
		}

		if ( ! isset( $schedules['everythirtyminutes'] ) ) {
			$schedules['everythirtyminutes'] = array(
				'interval' => 30 * 60,
				'display' => __( 'Every 30 Minutes', 'issslpg' )
			);
		}

		if ( ! isset( $schedules['everytwohours'] ) ) {
			$schedules['everytwohours'] = array(
				'interval' => 120 * 60,
				'display' => __( 'Every 2 Hours', 'issslpg' )
			);
		}

		return $schedules;
	}

	/**
	 * Schedule Landing Page Updates.
	 *
	 * @wp-hook init
	 */
	public function schedule_landing_page_updates() {
		$throttle = ISSSLPG_Options::get_setting( 'landing_page_throttle' );

		switch( $throttle ) {
			case '1' :
				$speed = 'daily';
				break;
			case '10' :
				$speed = 'everytwohours';
				break;
			case '100' :
				$speed = 'everytenminutes';
				break;
			case '1000' :
			default :
				$speed = 'everyminute';
		}

		if ( ! wp_next_scheduled( 'issslpg_schedule_landing_page_updates' ) ) {
			wp_schedule_event( time(), $speed, 'issslpg_schedule_landing_page_updates' );
		}

		if ( ! $throttle ) {
			if ( ! wp_next_scheduled( 'issslpg_schedule_landing_page_bulk_updates' ) ) {
				wp_schedule_event( time(), $speed, 'issslpg_schedule_landing_page_bulk_updates' );
			}
		}

		if ( ! wp_next_scheduled( 'issslpg_schedule_change_landing_pages_status' ) ) {
			wp_schedule_event( time(), $speed, 'issslpg_schedule_change_landing_pages_status' );
		}
	}

	/**
	 * Reset beat.
	 *
	 * @wp-hook wp_loaded
	 */
	public function maybe_set_next_beat() {
		ISSSLPG_Admin_Beat::maybe_set_next_beat();
//		ISSSLPG_Logger::log( 'maybe_set_next_beat hook executed', __METHOD__ );
	}

	/**
	 * Update Landing Pages.
	 *
	 * @wp-hook issslpg_schedule_landing_page_updates
	 * @wp-hook wp_loaded
	 */
	public function update_landing_pages() {
		if ( ISSSLPG_Admin_Beat::past_beat() ) {
			ISSSLPG_Admin_Scheduled_Tasks::update_landing_pages();
			ISSSLPG_Logger::log( 'issslpg_schedule_landing_page_updates hook executed', __METHOD__ );
		}
	}

	/**
	 * Update Single Landing Page.
	 *
	 * @wp-hook wp_loaded
	 */
	public function update_single_landing_page() {
		$throttle = ISSSLPG_Options::get_setting( 'landing_page_throttle' );
		if ( ( ! $throttle || $throttle == '1000' ) && ISSSLPG_Admin_Beat::past_beat() ) {
			ISSSLPG_Admin_Scheduled_Tasks::update_landing_pages();
			ISSSLPG_Logger::log( 'wp_loaded hook executed to update single landing page', __METHOD__ );
		}
	}

	/**
	 * Update Custom Location Landing Pages.
	 *
	 * @wp-hook issslpg_schedule_landing_page_updates
	 * @wp-hook wp_loaded
	 */
	public function update_custom_location_landing_pages() {
		if ( ISSSLPG_Admin_Beat::past_beat() ) {
			ISSSLPG_Logger::log( 'issslpg_schedule_landing_page_updates hook executed', __METHOD__ );
			ISSSLPG_Admin_Scheduled_Tasks::update_custom_location_landing_pages();
		}
	}

	/**
	 * Bulk Update Landing Pages.
	 *
	 * @wp-hook issslpg_schedule_landing_page_bulk_updates
	 * @wp-hook wp_loaded
	 */
	public function bulk_update_landing_pages() {
		if ( ISSSLPG_Admin_Beat::past_beat() ) {
			ISSSLPG_Logger::log( 'issslpg_schedule_landing_page_bulk_updates hook executed', __METHOD__ );
			ISSSLPG_Admin_Scheduled_Tasks::bulk_update_landing_pages();
		}
	}

	/**
	 * Change Landing Pages Status.
	 *
	 * @wp-hook issslpg_schedule_landing_page_updates
	 * @wp-hook wp_loaded
	 */
	public function change_landing_pages_status() {
		if ( ISSSLPG_Admin_Beat::past_beat() ) {
			ISSSLPG_Logger::log( 'change_landing_pages_status function executed', __METHOD__ );
			ISSSLPG_Admin_Scheduled_Tasks::change_landing_pages_status();
		}
	}

	/**
	 * Maybe download Custom Locations file.
	 *
	 * @wp-hook plugins_loaded
	 */
	public function maybe_download_custom_locations_file() {
		if ( ! is_admin() || ! isset( $_GET['export_custom_locations'] ) || ! isset( $_GET['county_id'] ) ) {
			return;
		}

		$county_data = new ISSSLPG_County_Data( intval( $_GET['county_id'] ) );
		$file_name = sanitize_title( $county_data->name ) . '-county-custom-locations';
		$custom_locations = $county_data->custom_locations;

		if ( ! $custom_locations ) {
			 return false;
		}

		$file_content = ISSSLPG_Admin_CSV_Helper::array_to_csv( $custom_locations );
		$downloader = new ISSSLPG_File_Downloader();
		$downloader->download_file( $file_name, 'csv', 'text/csv', $file_content );
	}

	/**
	 * Maybe download HTML Sitemap CSV file.
	 *
	 * @wp-hook plugins_loaded
	 */
	public function maybe_download_html_sitemap_csv() {
		if ( ! is_admin() || ! isset( $_GET['export_html_sitemap_csv'] ) ) {
			return;
		}
		if ( ! isset( $_GET['export_html_sitemap_template'] ) || empty( $_GET['export_html_sitemap_template'] ) ) {
			return;
		}

		$template_page_id = intval( $_GET['export_html_sitemap_template'] );
		// $landing_pages = get_posts( array(
		// 	'post_type'      => 'issslpg-landing-page',
		// 	'post_status'    => 'publish',
		// 	'posts_per_page' => -1,
		// 	'meta_query'     => array(
		// 		// 'relation' => 'AND',
		// 		array(
		// 			'key'     => '_issslpg_template_page_id',
		// 			'value'   => $template_page_id,
		// 			'compare' => '=',
		// 		),
		// 	),
		// ) );

		global $wpdb;
		$landing_pages = $wpdb->get_results("
			SELECT ID, post_title
			FROM $wpdb->posts, $wpdb->postmeta
			WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id
			AND $wpdb->postmeta.meta_key = '_issslpg_template_page_id'
			AND $wpdb->postmeta.meta_value = '$template_page_id'
			AND $wpdb->posts.post_status = 'publish'
			AND $wpdb->posts.post_type = 'issslpg-landing-page'
		", OBJECT);

		$content = array();
		foreach( $landing_pages as $landing_page ) {
			$title     = $landing_page->post_title;
			$permalink = get_permalink( $landing_page->ID );
			$link = "=HYPERLINK(\"\"{$permalink}\"\", \"\"{$title}\"\")";
			// $anchor    = "=HYPERLINK('{$permalink}'){$title}</a>";
			$content[] = array(
				'Title'     => $title,
				'Hyperlink' => $link,
				'Permalink' => $permalink,
				// 'Anchor'    => $anchor,
			);
		}
		// foreach( $landing_pages as $landing_page ) {
		// 	$title     = get_the_title( $landing_page );
		// 	$permalink = get_the_permalink( $landing_page );
		// 	$anchor    = "<a href='{$permalink}'>{$title}</a>";
		// 	$content[] = array(
		// 		'Title'     => $title,
		// 		'Permalink' => $permalink,
		// 		'Anchor'    => $anchor,
		// 	);
		// }

		$file_content = ISSSLPG_Admin_CSV_Helper::array_to_csv( $content );
		$template_page_title_slug = sanitize_title( get_the_title( $template_page_id ) );
		$file_name =  "{$template_page_title_slug}-sitemap";

		$downloader = new ISSSLPG_File_Downloader();
		$downloader->download_file( $file_name, 'csv', 'text/csv', $file_content );
	}

	/**
	 * Add body classes
	 *
	 * @wp-hook admin_body_class
	 */
	public function add_body_classes( $classes ) {
		if ( ISSSLPG_Helpers::is_simulated_plan() ) {
			$classes.= ' issslpg-simulated-plan ';
			$classes.= ' js-issslpg-simulated-plan ';
		}
		if ( ISSSLPG_Helpers::is_white_labeled() ) {
			$classes.= ' issslpg-white-labeled ';
			$classes.= ' js-issslpg-white-labeled ';
		}

		return $classes;
	}

	/**
	 * Register admin notices
	 *
	 * @wp-hook admin_notices
	 */
	public function register_admin_notices() {
		ISSSLPG_Admin_Notices::edit_page_update_button_reminder();
		ISSSLPG_Admin_Notices::free_plan_content_block_limit_upgrade_notice();
		ISSSLPG_Admin_Notices::free_plan_county_limit_upgrade_notice();
		ISSSLPG_Admin_Notices::documentation_notice();
		ISSSLPG_Admin_Notices::debug_notice();
	}

	/**
	 * Register TinyMCE Shortcode Button
	 *
	 * @wp-hook admin_init
	 */
	public function register_tinymce_shortcode_button() {
		new ISSSLPG_Admin_Register_TinyMCE_Shortcode_Button();
	}

	/**
	 * Add alt text shortcodes to images.
	 *
	 * Automatically adds a shortcode as alt text to images attached to Landing
	 * Page Generator posts.
	 *
	 * Source: https://brutalbusiness.com/automatically-set-the-wordpress-image-title-alt-text-other-meta/
	 *
	 * @wp-hook add_attachment
	 */
	function add_alt_text_shortcodes_to_images( $attachment_id ) {
		if ( wp_attachment_is_image( $attachment_id ) ) {
			$post_id = get_post( $attachment_id )->post_parent;
			switch ( get_post_type( $post_id ) ) {
				case 'issslpg-template' :
				case 'issslpg-landing-page' :
				case 'issslpg-local' :
					update_post_meta( $attachment_id, '_wp_attachment_image_alt', '[iss_alt_text_page_title_city_state]' );
					break;
			}
		}
	}

	/**
	 * On save post.
	 *
	 * @wp-hook save_post
	 */
	public function on_save_post( $page_id ) {
		ISSSLPG_Logger::log( 'save_post hook executed for page: ' . get_the_title( $page_id ), __METHOD__ );
		ISSSLPG_Landing_Page::maybe_activate_or_deactivate_city( $page_id );
//		ISSSLPG_Template_Page::create_or_update_corralating_landing_pages( $page_id );
		ISSSLPG_Template_Page::maybe_delete_corralating_landing_pages( $page_id );
	}

	/**
	 * On post updated.
	 *
	 * @wp-hook post_updated
	 */
	public function on_post_updated( $page_id, $page_after, $page_before ) {
		if ( ! ISSSLPG_Template_Page::is_template_page( $page_id ) ) {
			return false;
		}

		ISSSLPG_Logger::log( 'on_post_updated hook executed for page: ' . get_the_title( $page_id ), __METHOD__ );

		// Set timestamp for caching manager
		update_post_meta( $page_id, '_issslpg_last_updated', time() );

		// When a new template page is created...
//			if ( ! isset( $page_before ) && isset( $page_after ) ) {
//				ISSSLPG_Template_Page::create_or_update_corralating_landing_pages( $page_id );
//			}

		// When a template page is renamed...
		if ( isset( $page_before ) && isset( $page_after ) ) {
			if ( $page_before->post_title != $page_after->post_title ) {
				ISSSLPG_Template_Page::create_or_update_corralating_landing_pages( $page_id );
			}
		}

		// Only update landing pages that are correlated with the template page,
		// if the title actually changed.
//		if ( ISSSLPG_Template_Page::is_template_page( $page_id ) ) {
//			if ( isset( $page_before ) && isset( $page_after ) ) {
//				if ( $page_before->post_title != $page_after->post_title ) {
//					ISSSLPG_Template_Page::create_or_update_corralating_landing_pages( $page_id );
//				}
//			}
//		}
	}

	/**
	 * On Update Landing Page Throttle.
	 *
	 * Gets called when landing page throttle setting is updated.
	 */
	public function on_update_landing_page_throttle() {
		// Unschedule events, so we can schedule them again with the updated throttle speed.
		wp_clear_scheduled_hook( 'issslpg_schedule_landing_page_updates' );
		wp_clear_scheduled_hook( 'issslpg_schedule_landing_page_bulk_updates' );
		wp_clear_scheduled_hook( 'issslpg_schedule_change_landing_pages_status' );
		ISSSLPG_Logger::log( 'Landing page throttle setting was updated', __METHOD__ );
	}

	/**
	 * Add links to the plugin description.
	 *
	 * @wp-hook plugin_action_links
	 */
	public function add_action_links( $links ) {
		if ( ! ISSSLPG_Helpers::is_white_labeled() && ! ISSSLPG_Helpers::is_simulated_plan() && ISSSLPG_Helpers::is_plan( 'basic' ) ) {
			$url = admin_url( 'admin.php?page=issslpg_location_settings-pricing' );
			$link = "<a href='{$url}'><b>" . __( 'Buy License' ) . '</b></a>';
			array_unshift( $links, $link );
		}
		return $links;
	}

	/**
	 * On update landing page title format options.
	 *
	 * @wp-hook cmb2_save_field_landing_page_heading_format
	 */
	public function on_update_landing_page_title_format_options( $field_id, $updated, $action ) {
		if ( $updated ) {
			ISSSLPG_Landing_Page::update_all_landing_pages();
//			ISSSLPG_Landing_Page::update_all_custom_location_landing_pages();
		}
	}

	/**
	 * On update landing page slug option.
	 *
	 * @wp-hook cmb2_save_field_landing_page_slug
	 */
	public function on_update_landing_page_landing_page_slug( $field_id, $updated, $action ) {
		if ( $updated ) {
			add_option( 'issslpg_flush_rewrite_rules_flag', true );
		}
	}

	/**
	 * On update HTML sitemap slug option.
	 *
	 * @wp-hook cmb2_save_field_html_sitemap_slug
	 */
	public function on_update_html_sitemap_slug( $field_id, $updated, $action ) {
		if ( $updated ) {
			add_option( 'issslpg_flush_rewrite_rules_flag', true );
		}
	}

	/**
	 * On trashed post.
	 *
	 * @wp-hook trashed_post
	 */
	public function on_trashed_post( $page_id ) {
		ISSSLPG_Template_Page::delete_corralating_landing_pages( $page_id );
		ISSSLPG_Landing_Page::maybe_activate_or_deactivate_city( $page_id );
		ISSSLPG_Logger::log( 'trashed_post hook was executed for page: ' . get_the_title( $page_id ), __METHOD__ );
	}

	/**
	 * On untrash post.
	 *
	 * @wp-hook untrash_post
	 */
	public function on_untrash_post( $page_id ) {
//		ISSSLPG_Landing_Page::maybe_activate_or_deactivate_city( $page_id );
//		ISSSLPG_Landing_Page::update( array() );
//		ISSSLPG_Logger::log( 'untrash_post hook was executed', __METHOD__ );
	}

	/**
	 * Register post types.
	 *
	 * @wp-hook init
	 */
	public function register_post_types() {
		new ISSSLPG_Admin_Template_Page_Post_Type_Registration();
		new ISSSLPG_Admin_Landing_Page_Post_Type_Registration();
		if ( ISSSLPG_Helpers::is_local_content_usage_allowed() ) {
			new ISSSLPG_Admin_Local_Content_Post_Type_Registration();
		}
	}

	/**
	 * Register CMB2 meta fields.
	 *
	 * @wp-hook cmb2_admin_init
	 */
	public function register_cmb2_meta_fields() {
		new ISSSLPG_Admin_CMB2_Plugin_Limited_Meta_Field_Registration();
		new ISSSLPG_Admin_CMB2_Plugin_Meta_Box_Registration();
	}

	/**
	 * Register CMB2 custom fields.
	 *
	 * @wp-hook cmb2_admin_init
	 */
	public function register_cmb2_custom_fields() {
		new ISSSLPG_Admin_CMB2_Plugin_Custom_Fields_Registration();
		ISSSLPG_Admin_CMB2_Plugin_Render_Business_Day_Hours_Field::init();
	}

	/**
	 * Register CMB2 settings page.
	 *
	 * @wp-hook cmb2_admin_init
	 */
	public function register_cmb2_settings_page() {
		new ISSSLPG_Admin_CMB2_Plugin_Settings_Page_Registration();
	}

	/**
	 * Register location settings page.
	 *
	 * @wp-hook init
	 */
	public function register_location_settings_page() {
		new ISSSLPG_Admin_Location_Settings_Page();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @wp-hook admin_enqueue_scripts
	 */
	public function enqueue_styles() {

		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/issslpg-admin.css',
			array(),
			$this->version,
			'all'
		);

		wp_enqueue_style(
			'iss_sweetalert2',
			plugin_dir_url( __FILE__ ) . 'plugins/sweetalert2/sweetalert2.min.css',
			array(),
			$this->version,
			'all'
		);

		// Enqueue lightgallery plugin
		wp_enqueue_style(
			'iss_lightgallery',
			plugin_dir_url( __FILE__ ) . 'plugins/lightgallery/dist/css/lightgallery.min.css',
			array(),
			$this->version,
			'all'
		);

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @wp-hook admin_enqueue_scripts
	 */
	public function enqueue_scripts() {

		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/issslpg-admin.js',
			array( 'jquery' ),
			$this->version,
			true
		);

		wp_enqueue_script(
			'iss_sweetalert2',
			plugin_dir_url( __FILE__ ) . 'plugins/sweetalert2/sweetalert2.all.min.js',
			array(),
			$this->version,
			false
		);

		// Enqueue lightgallery plugin
		wp_enqueue_script(
			'iss_lightgallery',
			plugin_dir_url( __FILE__ ) . 'plugins/lightgallery/dist/js/lightgallery.min.js',
			array( 'jquery', $this->plugin_name ),
			$this->version,
			false
		);

		// Enqueue lightgallery video plugin
		wp_enqueue_script(
			'iss_lightgallery-all',
			plugin_dir_url( __FILE__ ) . 'plugins/lightgallery/dist/js/lightgallery-all.min.js',
			array( 'jquery', $this->plugin_name, 'iss_lightgallery' ),
			$this->version,
			false
		);

		wp_enqueue_script(
			'iss_sortable',
			plugin_dir_url( __FILE__ ) . 'plugins/sortable/sortable.js',
			array(),
			$this->version,
			false
		);

	}

}
