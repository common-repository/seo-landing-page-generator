<?php



/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://intellasoftplugins.com
 * @since      1.0.0
 *
 * @package    ISSSLPG
 * @subpackage ISSSLPG/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    ISSSLPG
 * @subpackage ISSSLPG/includes
 * @author     Ruven Pelka <ruven.pelka@gmail.com>
 */
class ISSSLPG {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      ISSSLPG_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'ISSSLPG_VERSION' ) ) {
			$this->version = ISSSLPG_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'issslpg';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - ISSSLPG_Loader. Orchestrates the hooks of the plugin.
	 * - ISSSLPG_i18n. Defines internationalization functionality.
	 * - ISSSLPG_Admin. Defines all hooks for the admin area.
	 * - ISSSLPG_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for the Composer autoloader.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/vendor/autoload.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/models/class-city.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/models/class-city-data.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/models/class-city-demographics.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/models/class-country.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/models/class-country-data.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/models/class-county.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/models/class-county-data.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/models/class-county-demographics.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/models/class-state.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/models/class-state-data.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/models/class-state-demographics.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/models/class-zip-codes.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issslpg-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issslpg-i18n.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/remote_downloader/class-issslpg-data-seeder.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/remote_downloader/class-issslpg-download-queue.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/remote_downloader/class-issslpg-download-queue-item.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/remote_downloader/class-issslpg-remote-data.php';
//		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/remote_downloader/class-issslpg-remote-data-db.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/remote_downloader/class-issslpg-remote-data-downloader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issslpg-landing-page-api.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issslpg-location-data.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issslpg-logger.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issslpg-city-data.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issslpg-county-data.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issslpg-state-data.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issslpg-country-data.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issslpg-url-helpers.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issslpg-database-tables.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issslpg-options.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issslpg-helpers.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issslpg-landing-page.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issslpg-template-page.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issslpg-meta-data.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issslpg-issscr-functions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issslpg-issscr-sample-content.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issslpg-file-downloader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issslpg-array-helpers.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issslpg-string-helpers.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issslpg-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-issslpg-public.php';

		$this->loader = new ISSSLPG_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the ISSSLPG_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new ISSSLPG_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$admin_plugin = new ISSSLPG_Admin( $this->plugin_name, $this->version );
		$plugin_basename = ISSSLPG_BASENAME;

		// WP Action Hooks
		$this->loader->add_action( 'init',                  $admin_plugin, 'register_location_settings_page'                         );
		$this->loader->add_action( 'init',                  $admin_plugin, 'register_post_types'                                     );
		$this->loader->add_action( 'init',                  $admin_plugin, 'schedule_landing_page_updates',                    20, 0 );
//		$this->loader->add_action( 'admin_init',            $admin_plugin, 'maybe_seed_download_queue_with_demographics_slot', 20, 0 );
		$this->loader->add_action( 'admin_init',            $admin_plugin, 'maybe_download_custom_locations_file'                    );
		$this->loader->add_action( 'admin_init',            $admin_plugin, 'maybe_download_html_sitemap_csv'                         );
		$this->loader->add_action( 'admin_init',            $admin_plugin, 'register_tinymce_shortcode_button'                       );
		$this->loader->add_action( 'widgets_init',          $admin_plugin, 'register_widgets'                                        );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin_plugin, 'localize_ajax_object',                             11, 1 );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin_plugin, 'enqueue_styles'                                          );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin_plugin, 'enqueue_scripts'                                         );
		$this->loader->add_action( 'admin_body_class',      $admin_plugin, 'add_body_classes'                                        );
		$this->loader->add_action( 'admin_notices',         $admin_plugin, 'register_admin_notices'                                  );
		$this->loader->add_action( 'admin_menu',            $admin_plugin, 'admin_menu',                                       99, 0 );

		// WP User Action Hooks
		$this->loader->add_action( 'save_post',      $admin_plugin, 'on_save_post',     9999, 1 );
		$this->loader->add_action( 'post_updated',   $admin_plugin, 'on_post_updated',  9999, 3 );
		$this->loader->add_action( 'trashed_post',   $admin_plugin, 'on_trashed_post'           );
		$this->loader->add_action( 'untrash_post',   $admin_plugin, 'on_untrash_post',  9999    );
		$this->loader->add_action( 'add_attachment', $admin_plugin, 'add_alt_text_shortcodes_to_images' );

		// Plugin Action Hooks
		$this->loader->add_action( 'cmb2_admin_init', $admin_plugin, 'register_cmb2_meta_fields',  9999 );
		$this->loader->add_action( 'cmb2_admin_init', $admin_plugin, 'register_cmb2_settings_page'      );
		$this->loader->add_action( 'cmb2_admin_init', $admin_plugin, 'register_cmb2_custom_fields'      );
//		$this->loader->add_action( 'cmb2_save_field_company_phone',                  $admin_plugin, 'on_update_landing_page_title_format_options', 10, 3 );
		$this->loader->add_action( 'cmb2_save_field_company_phone',                  $admin_plugin, 'on_update_landing_page_title_format_options', 10, 3 );
		$this->loader->add_action( 'cmb2_save_field_landing_page_throttle',          $admin_plugin, 'on_update_landing_page_throttle',             10, 3 );
		$this->loader->add_action( 'cmb2_save_field_landing_page_heading_format',    $admin_plugin, 'on_update_landing_page_title_format_options', 10, 3 );
		$this->loader->add_action( 'cmb2_save_field_landing_page_page_title_format', $admin_plugin, 'on_update_landing_page_title_format_options', 10, 3 );
		$this->loader->add_action( 'cmb2_save_field_landing_page_slug',              $admin_plugin, 'on_update_landing_page_landing_page_slug',    10, 3 );
		$this->loader->add_action( 'cmb2_save_field_html_sitemap_slug',              $admin_plugin, 'on_update_html_sitemap_slug',                 10, 3 );

		// Custom Action Hooks
		$this->loader->add_action( 'issslpg_schedule_landing_page_updates',        $admin_plugin, 'update_landing_pages',                 20, 0 );
		$this->loader->add_action( 'issslpg_schedule_landing_page_updates',        $admin_plugin, 'update_custom_location_landing_pages', 20, 0 );
//		$this->loader->add_action( 'issslpg_schedule_landing_page_bulk_updates',   $admin_plugin, 'bulk_update_landing_pages',            20, 0 );
		$this->loader->add_action( 'issslpg_schedule_change_landing_pages_status', $admin_plugin, 'change_landing_pages_status',          20, 0 );
		$this->loader->add_action( 'wp_loaded', $admin_plugin, 'bulk_update_landing_pages', 20, 0 );
		$this->loader->add_action( 'wp_loaded', $admin_plugin, 'change_landing_pages_status', 21, 0 );
		$this->loader->add_action( 'wp_loaded', $admin_plugin, 'update_single_landing_page', 22, 0 );
		$this->loader->add_action( 'wp_loaded', $admin_plugin, 'maybe_set_next_beat', 23, 0 );

		// AJAX Hooks
		$this->loader->add_action( 'wp_ajax_download_remote_unit', $admin_plugin, 'download_remote_unit' );
		$this->loader->add_action( 'wp_ajax_update_download_queue', $admin_plugin, 'update_download_queue' );

		// WP Filters
		$this->loader->add_filter( 'cron_schedules', $admin_plugin, 'add_cron_schedules', 10, 1 );
		$this->loader->add_filter( "plugin_action_links_{$plugin_basename}", $admin_plugin, 'add_action_links' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$public_plugin = new ISSSLPG_Public( $this->plugin_name, $this->version );

		// WP Action Hooks
		$this->loader->add_action( 'the_post',           $public_plugin, 'remove_autop',                            20, 0 );
		$this->loader->add_action( 'init',               $public_plugin, 'register_routing_methods',                10, 0 );
		$this->loader->add_action( 'init',               $public_plugin, 'schedule_xml_sitemaps_update',            20, 0 );
		$this->loader->add_action( 'wp',                 $public_plugin, 'maybe_download_landing_page_html_file', 9999, 0 );
		$this->loader->add_action( 'wp',                 $public_plugin, 'set_landing_page_id',                     20, 0 );
		$this->loader->add_action( 'wp',                 $public_plugin, 'register_shortcodes'                                                );
		$this->loader->add_action( 'wp_head',            $public_plugin, 'maybe_add_schema_data',                   10, 0 );
		$this->loader->add_action( 'wp_enqueue_scripts', $public_plugin, 'add_landing_page_blockstyles'                                      );
		$this->loader->add_action( 'wp_enqueue_scripts', $public_plugin, 'enqueue_styles'                                                    );
		$this->loader->add_action( 'wp_enqueue_scripts', $public_plugin, 'enqueue_scripts'                                                   );

		// Custom Action Hooks
		$this->loader->add_action( 'issslpg_schedule_xml_sitemap_update', $public_plugin, 'create_xml_sitemaps', 20, 0 );
//		$this->loader->add_action( 'init', $public_plugin, 'create_xml_sitemaps', 20, 0 ); // Un-comment to create sitemaps right away (for debug purposes)

		// WP Filters
		$this->loader->add_filter( 'the_content',            $public_plugin, 'modify_content',                 9, 1 );
		$this->loader->add_filter( 'document_title_parts',   $public_plugin, 'modify_document_title_part',  9999, 1 );
		$this->loader->add_filter( 'pre_get_document_title', $public_plugin, 'modify_legacy_title',           20, 1 );
		$this->loader->add_filter( 'wp_title',               $public_plugin, 'modify_legacy_title',         9999, 1 );
//		$this->loader->add_filter( 'wp_footer',              $public_plugin, 'save_cache_record',           9999, 0 );
		$this->loader->add_filter( 'template_include',       $public_plugin, 'landing_page_theme_template',   99, 1 );
		add_filter( 'widget_text', 'do_shortcode' ); // Activate shortcodes in widgets
		add_filter( 'the_excerpt', 'do_shortcode' ); // Activate shortcodes in excerpts
		add_filter( 'get_the_excerpt', 'do_shortcode' ); // Activate shortcodes in excerpts
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    ISSSLPG_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
