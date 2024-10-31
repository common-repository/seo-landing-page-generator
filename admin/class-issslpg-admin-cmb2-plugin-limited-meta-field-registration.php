<?php



/**
 * Source: https://github.com/CMB2/CMB2-Snippet-Library/blob/master/javascript/limit-number-of-multiple-repeat-groups.php
 */
class ISSSLPG_Admin_CMB2_Plugin_Limited_Meta_Field_Registration {

	public function __construct() {
		$this->register_limited_repeater_metaboxes();
	}

	public function register_limited_repeater_metaboxes() {
		$limited_repeater_metaboxes = array(
			'issslpg_large_market_content_panel',
			'issslpg_alternative_large_market_content_panel',
			'issslpg_local_static_content_panel',
			'issslpg_general_images_panel',
		);
		$dynamic_metaboxes = $this->get_dynamic_metaboxes();
		foreach( $dynamic_metaboxes as $dynamic_metabox ) {
			$limited_repeater_metaboxes[] = $dynamic_metabox;
		}
//		array_push( $limited_repeater_metaboxes, $dynamic_metaboxes );
//		var_dump($limited_repeater_metaboxes);
		foreach ( $limited_repeater_metaboxes as $metaboxes ) {
			add_action( "cmb2_after_post_form_{$metaboxes}", array( $this, 'limit_group_repeat' ), 10, 2 );
		}
	}

	public function get_dynamic_metaboxes() {
//		$dynamic_panel_array = array();
		$dynamic_panel_handles = array();
		$dynamic_content_panels = ISSSLPG_Options::get_panels( "landing_page_content_panels" );
		$dynamic_image_panels   = ISSSLPG_Options::get_panels( "landing_page_image_panels" );
//		$dynamic_keyword_panels = ISSSLPG_Options::get_panels( "landing_page_keyword_panels" );

		foreach ( $dynamic_content_panels as $dynamic_content_panel ) {
			$dynamic_panel_handles[] = "issslpg_{$dynamic_content_panel['handle']}_content_panel";
		}
		foreach ( $dynamic_image_panels as $dynamic_image_panel ) {
			$dynamic_panel_handles[] = "issslpg_{$dynamic_image_panel['handle']}_images_panel";
		}
//		foreach ( $dynamic_keyword_panels as $dynamic_keyword_panel ) {
//			$dynamic_panel_handles[] = "issslpg_{$dynamic_keyword_panel['handle']}_keywords_panel";
//		}

//		array_push( $dynamic_panel_array, $dynamic_content_panels, $dynamic_image_panels, $dynamic_keyword_panels );

//		foreach( $dynamic_panel_array as $dynamic_panels ) {
//			foreach( $dynamic_panels as $dynamic_panel ) {
//				$dynamic_panel_handles[] = "issslpg_{$dynamic_panel['handle']}_panel";
//			}
//		}

		return $dynamic_panel_handles;
	}

	public function limit_group_repeat( $post_id, $cmb ) {
		// Grab the custom attribute to determine the limit
		$limit = absint( $cmb->prop( 'rows_limit' ) );
		$limit = $limit ? $limit : 0;
		$group = $cmb->prop( 'id' );
		?>
		<script type="text/javascript">
			jQuery(document).ready( function($) {
				// Only allow 3 groups
				var limit             = <?php echo $limit; ?>;
				var fieldGroupId      = '#<?php echo $group; ?>';
				var fieldGroupTableId = $('.cmb-repeatable-group', fieldGroupId).attr('id');
				var $fieldGroupTable  = $('.cmb-repeatable-group', fieldGroupId);
//				var $fieldGroupTable  = $( document.getElementById( fieldGroupTableId ) );
//				var $fieldGroupTable  = $( document.getElementById( fieldGroupId + '_repeat' ) );
//				console.log(fieldGroupId);
//				console.log($fieldGroupTable);
				var countRows = function() {
					return $fieldGroupTable.find( '> .cmb-row.cmb-repeatable-grouping' ).length;
				};
				var disableAdder = function() {
					$fieldGroupTable.find('.cmb-add-group-row.button-secondary').prop( {disabled: true} );
				};
				var enableAdder = function() {
					$fieldGroupTable.find('.cmb-add-group-row.button-secondary').prop( {disabled: false} );
				};
				$fieldGroupTable
						.ready( function() {
							if ( countRows() >= limit ) {
								disableAdder();
							}
							else if ( countRows() < limit ) {
								enableAdder();
							}
						})
						.on( 'cmb2_add_row', function() {
							if ( countRows() >= limit ) {
								disableAdder();
							}
						})
						.on( 'cmb2_remove_row', function() {
							if ( countRows() < limit ) {
								enableAdder();
							}
						});
//				$fieldGroupTable
//						.ready( function() {
//							if ( countRows() >= limit ) {
//								disableAdder();
//							}
//							else if ( countRows() < limit ) {
//								enableAdder();
//							}
//						});
			});
		</script>
		<?php
	}

}