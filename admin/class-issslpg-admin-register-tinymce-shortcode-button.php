<?php



class ISSSLPG_Admin_Register_TinyMCE_Shortcode_Button {


	function __construct() {

		// Check user permissions
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		// Check if we can get post ID
		if ( ! isset( $_GET['post'] ) || ! isset( $_GET['action'] ) ) {
			return;
		}

		// Check if we're in the admin panel and randomization is enabled
		if ( ! is_admin() || $_GET['action'] != 'edit' ) {
			return;
		}

		$post_type = get_post_type( $_GET['post'] );
		$is_issslpg_edit_page = ( 'issslpg-template' == $post_type || 'issslpg-landing-page' == $post_type || 'issslpg-local' == $post_type );
		if ( $is_issslpg_edit_page ) {
			add_action( 'admin_head', array( &$this, 'add_mce_button' ) );
			add_action( 'admin_head', array( &$this, 'localize_script' ) );
		}
	}


	function add_mce_button() {

		// Check if WYSIWYG editor is enabled
		if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', array( &$this, 'add_mce_plugin' ) );
			add_filter( 'mce_buttons', array( &$this, 'register_mce_button' ) );
		}
	}

	function add_mce_plugin( $plugin_array ) {
		$plugin_array['issslpg_tinymce_shortcode_button'] = plugins_url( 'js/issslpg-admin-tinymce.js', __FILE__ );
		return $plugin_array;
	}

	function register_mce_button( $buttons ) {
		array_push( $buttons, 'issslpg_tinymce_shortcode_button' );
		return $buttons;
	}

	function localize_script() {
		$post_type = get_post_type( get_the_ID() );
		$is_cta_button_usage_allowed         = 'false';
		$is_demographics_usage_allowed       = 'false';
		$is_faq_usage_allowed                = 'false';
		$on_template_page                    = 'false';
		$show_large_market_content_panel     = 'false';
		$show_alt_large_market_content_panel = 'false';
		$show_local_static_content_panel     = 'false';
		$show_local_images_panel             = 'false';

		if ( ISSSLPG_Helpers::is_cta_button_usage_allowed() ) {
			$is_cta_button_usage_allowed = 'true';
		}
		if ( ISSSLPG_Helpers::is_demographics_usage_allowed() ) {
			$is_demographics_usage_allowed = 'true';
		}
		if ( ISSSLPG_Helpers::is_faq_usage_allowed() ) {
			$is_faq_usage_allowed = 'true';
		}
		if ( 'issslpg-template' == $post_type  ) {
			$on_template_page = 'true';
		}
		if ( ISSSLPG_Options::get_setting( 'show_landing_page_large_market_content_panel' ) ) {
			$show_large_market_content_panel = 'true';
		}
		if ( ISSSLPG_Options::get_setting( 'show_landing_page_alternative_large_market_content_panel', true ) ) {
			$show_alt_large_market_content_panel = 'true';
		}
		if ( ISSSLPG_Options::get_setting( 'show_landing_page_local_static_content_panel', true ) ) {
			$show_local_static_content_panel = 'true';
		}
		if ( ISSSLPG_Options::get_setting( 'show_landing_page_local_images_content_panel', true ) ) {
			$show_local_images_panel = 'true';
		}
		?>
			<script type="text/javascript">
				var issslpg_shortcode_data = '<?php echo json_encode( ISSSLPG_Public_Shortcode_Helpers::get_dynamic_shortcode_data( false ) ); ?>';
				var issslpg_active_shortcodes = {
					is_cta_button_usage_allowed: <?php echo $is_cta_button_usage_allowed; ?>,
					is_demographics_usage_allowed: <?php echo $is_demographics_usage_allowed; ?>,
					is_faq_usage_allowed: <?php echo $is_faq_usage_allowed; ?>,
					on_template_page: <?php echo $on_template_page; ?>,
					large_market_content: <?php echo $show_large_market_content_panel; ?>,
					alt_large_market_content: <?php echo $show_alt_large_market_content_panel; ?>,
					local_static_content: <?php echo $show_local_static_content_panel; ?>,
					local_images: <?php echo $show_local_images_panel; ?>,
				};
			</script>
		<?php
	}

}