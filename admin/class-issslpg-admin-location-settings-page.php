<?php

use WeDevs\ORM\WP\Country as Country;
use WeDevs\ORM\WP\State as State;
use WeDevs\ORM\WP\County as County;
use WeDevs\ORM\WP\CountyData as CountyData;
use WeDevs\ORM\WP\City as City;
use WeDevs\ORM\WP\CityData as CityData;

class ISSSLPG_Admin_Location_Settings_Page {

	private $plugin_id;

	private $options_handle;

	public function __construct() {
		$this->plugin_id = 'issslpg';
		$this->options_handle = 'issslpg_location_options';

		// We only need to register the admin panel on the back-end
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		}
	}

	private function output_breadcrumbs( $country = false, $state = false, $county = false, $city = false ) {
		if ( ! $country ) {
			return;
		}
		?>
		<div class="issslpg-breadcrumbs">
			<?php if ( $country ) : ?>
				<span class="issslpg-breadcrumbs-item">
					<a href="<?php echo admin_url( "admin.php?page={$this->plugin_id}_location_settings" ); ?>">Countries</a>
				</span>
				<span class="issslpg-breadcrumbs-separator">
					&raquo;
				</span>
				<span class="issslpg-breadcrumbs-item">
					<?php echo $country->name; ?>
				</span>
				<?php if ( $state ): ?>
					<span class="issslpg-breadcrumbs-separator">
						&raquo;
					</span>
					<span class="issslpg-breadcrumbs-item">
						<a href="<?php echo admin_url( "admin.php?page={$this->plugin_id}_location_settings&country_id={$country->id}" ); ?>">States</a>
					</span>
					<span class="issslpg-breadcrumbs-separator">
						&raquo;
					</span>
					<span class="issslpg-breadcrumbs-item">
						<?php echo $state->name; ?>
					</span>
					<?php if ( $county ) : ?>
						<span class="issslpg-breadcrumbs-separator">
							&raquo;
						</span>
						<span class="issslpg-breadcrumbs-item">
							<a href="<?php echo admin_url( "admin.php?page={$this->plugin_id}_location_settings&country_id={$country->id}&state_id={$state->id}" ); ?>">Counties</a>
						</span>
						<span class="issslpg-breadcrumbs-separator">
							&raquo;
						</span>
						<span class="issslpg-breadcrumbs-item">
							<?php echo $county->name; ?>
						</span>
						<?php if ( $city ) : ?>
							<span class="issslpg-breadcrumbs-separator">
								&raquo;
							</span>
							<span class="issslpg-breadcrumbs-item">
								<a href="<?php echo admin_url( "admin.php?page={$this->plugin_id}_location_settings&country_id={$country->id}&state_id={$state->id}&county_id={$county->id}" ); ?>">Cities</a>
							</span>
							<span class="issslpg-breadcrumbs-separator">
								&raquo;
							</span>
							<span class="issslpg-breadcrumbs-item">
								<?php echo $city->name; ?>
							</span>
						<?php endif; ?>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php
	}

	public function add_admin_menu() {
		add_menu_page(
			esc_html__( 'SEO Landing Page Generator', 'issslpg' ),
			esc_html__( 'SEO Landing Page Generator', 'issslpg' ),
			'manage_options',
			"{$this->plugin_id}_location_settings",
			array( $this, 'output_location_settings_page' )
		);

		add_submenu_page(
			null,
			esc_html__( 'Edit City', 'issslpg' ),
			esc_html__( 'Edit City', 'issslpg' ),
			'manage_options',
			"{$this->plugin_id}-edit-city",
			array( $this, 'output_edit_city_form' )
		);

		add_submenu_page(
			null,
			esc_html__( 'Edit County', 'issslpg' ),
			esc_html__( 'Edit County', 'issslpg' ),
			'manage_options',
			"{$this->plugin_id}-edit-county",
			array( $this, 'output_edit_county_form' )
		);

		add_submenu_page(
			null,
			esc_html__( 'Edit State', 'issslpg' ),
			esc_html__( 'Edit State', 'issslpg' ),
			'manage_options',
			"{$this->plugin_id}-edit-state",
			array( $this, 'output_edit_state_form' )
		);
	}

	public function output_location_settings_page() {
		if ( isset( $_GET['county_id'] ) ) {
			$this->output_cities_settings_page( intval( $_GET['county_id'] ) );
		}
		elseif ( isset( $_GET['state_id'] ) ) {
			$this->output_counties_settings_page( intval( $_GET['state_id'] ) );
		}
		elseif ( isset( $_GET['country_id'] ) ) {
			$this->output_states_settings_page( intval( $_GET['country_id'] ) );
		}
		else {
			$this->output_countries_settings_page();
		}
	}

	private function output_countries_settings_page() {
//		$countries = Country::all()->sortBy('name');
		$countries = Country::all()->sortBy( function( $a, $b ) {
			if ( $a->name == 'United States' ) {
				return -1;
			}
			return $a->name;
		} );
		if ( ! $countries ) {
			return false;
		}
		// Output:
		?>

		<div class="wrap">
			<h1><?php esc_html_e( 'Countries', 'issslpg' ); ?></h1>
			<?php $this->output_breadcrumbs(); ?>

			<form method="post" action="<?php echo admin_url( "admin.php?page=issslpg_location_settings&save=true" ); ?>">
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<td id="cb" class="manage-column column-cb check-column">
							</td>
							<th scope="col" id="title" class="column-title column-primary">
								<span>Country Name</span>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $countries as $country ) : ?>
							<?php
							$country_data = new ISSSLPG_Country_Data( $country );
							?>
							<tr valign="top">
								<th scope="row" class="issslpg-check-column  check-column">
									<!-- <input onclick="return false;" type="checkbox" name="<?php echo $this->options_handle ?>[active_states][<?php echo $country_data->id; ?>]" <?php checked( $country_data->status ); ?>> -->
									<!-- <input type="hidden" name="<?php echo $this->options_handle ?>[inactive_countries][<?php echo $country_data->id; ?>]" value="0"> -->
									<?php if ( $country_data->status ) : ?>
										<!-- <span class="dashicons dashicons-yes"></span> -->
										<span class="issslpg-active-indicator">&#9679;</span>
									<?php endif; ?>
								</th>
								<td>
									<a href="<?php echo add_query_arg( 'country_id', $country_data->id ); ?>">
										<?php echo esc_attr( $country_data->name ); ?>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</form>
		</div><!-- .wrap -->
		<?php
	}

	private function output_states_settings_page( $country_id ) {
		$country = Country::where( 'id', $country_id )->first();
		if ( ! $country ) {
			return false;
		}

		$states = $country->states;

		// Seed Download Queue for Demographics
//		global $wpdb;
//		$download_queue_table_name = "{$wpdb->prefix}issslpg_download_queue";
//		$pending_unit_ids = $wpdb->get_results( "SELECT DISTINCT unit_id FROM {$download_queue_table_name} WHERE unit_category = 'locations'", ARRAY_N );
//		foreach ( $pending_unit_ids as $pending_unit_id ) {
//			$remote_data_downloader = new ISSSLPG_Remote_Data_Downloader( array( 'demographics' ), $pending_unit_id[0] );
//			$remote_data_downloader->seed_queue();
//		}

		// If we want to save
		$saved = false;
		if ( ( isset( $_GET['save'] ) && $_GET['save'] == 'true' )
		     && isset( $_POST['issslpg_location_options'] ) ) {
			$saved = $this->save_states_settings_page();
		}

		// Set API Status
		$remote_data = new ISSSLPG_Remote_Data();
		$api_version = $remote_data->load_api_version();
		$api_status = 'working';
		if ( is_null( $api_version ) ) {
			$api_status = 'no-connection';
		} elseif ( $api_version != '1.0' ) {
			$api_status = 'out-of-date';
		}
		// Output:
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'States', 'issslpg' ); ?></h1>

			<?php $this->output_breadcrumbs( $country ); ?>

			<?php if ( $api_status == 'no-connection' ) : ?>
				<div id="message" class="notice notice-error">
					<p>There was a problem connection to the Location API. Please <a href="<?php echo admin_url( "plugins.php" ); ?>">update the plugin</a> to the newest version to be able to download location data.</p>
				</div>
			<?php elseif ( $api_status == 'out-of-date' ) : ?>
				<div id="message" class="notice notice-error">
					<p>The plugin is out-of-date. Please <a href="<?php echo admin_url( "plugins.php" ); ?>">update the plugin</a> to the newest version to be able to download location data.</p>
				</div>
			<?php endif; ?>

			<?php if ( $saved ) : ?>
				<div id="message" class="updated notice notice-success is-dismissible">
					<p>States updated.</p>
					<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
				</div>
			<?php endif; ?>

			<form method="post" action="<?php echo admin_url( "admin.php?page=issslpg_location_settings&save=true" ); ?>">
				<table class="wp-list-table  widefat  fixed  striped  issslpg-states-table">
					<thead>
						<tr>
							<!--
							<td id="cb" class="manage-column column-cb check-column">
								<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
								<input id="cb-select-all-1" type="checkbox">
							</td>
							-->
							<td id="cb" class="manage-column column-cb check-column">
							</td>
							<th scope="col" id="title" class="column-title column-primary" width="15%">
								<span>State Name</span>
							</th>
							<th scope="col">
							</th>
							<!--
							<td>
								Phone
							</td>
							-->
						</tr>
					</thead>
					<tbody>
						<?php
						$tr_order = array( 'downloaded', 'not-downloaded' )
						?>
						<?php foreach ( $tr_order as $order_item ) : ?>
							<?php foreach ( $states as $state ) : ?>
								<?php
								$state_data = new ISSSLPG_State_Data( $state );
								$download_unit_categories = array( 'locations', 'demographics' );
								$download_queue = new ISSSLPG_Download_Queue( $download_unit_categories, $state->id );
								$is_downloaded = $download_queue->is_complete();
								$download_progress = $download_queue->get_progress();
								$download_status = ( $is_downloaded ) ? 'done' : 'pending';
								$hide_download_icon_class = ( $is_downloaded ) ? 'issslpg-hide' : 'issslpg-show';
								$icon_class = ( $is_downloaded ) ? 'dashicons-arrow-right-alt' : 'dashicons-download';
								$button_visibility_class = ( $is_downloaded ) ? 'issslpg-hide' : 'issslpg-show';
								$link_visibility_class = ( $is_downloaded ) ? 'issslpg-show' : 'issslpg-hide';
								$hide_progress_bar_class = ( $download_progress == 0 || $download_progress == 100 ) ? 'issslpg-hide' : '';
								// $link_class = ( $is_downloaded ) ? 'issslpg-downloaded' : 'issslpg-download-pending';
								?>
								<?php
								// How downloaded items before not downloaded items
								if ( $order_item == 'not-downloaded' && $is_downloaded ) { continue; }
								if ( $order_item == 'downloaded' && ! $is_downloaded ) { continue; }
								?>
								<tr valign="top">
									<th scope="row" class="issslpg-check-column  check-column">
										<!-- <input onclick="return false;" type="checkbox" name="<?php echo $this->options_handle ?>[active_states][<?php echo $state_data->id; ?>]" <?php checked( $state_data->status ); ?>> -->
										<!-- <input type="hidden" name="<?php echo $this->options_handle ?>[inactive_states][<?php echo $state_data->id; ?>]" value="0"> -->
										<?php if ( $state_data->status ) : ?>
											<!-- <span class="dashicons dashicons-yes"></span> -->
											<span class="issslpg-active-indicator">&#9679;</span>
										<?php endif; ?>
									</th>
									<td>
										<!-- State Download Button -->
										<a href="#"
										   class="js-issslpg-state-download-button  issslpg-state-download-button  button button-secondary button-small <?php echo $button_visibility_class; ?>"
										   data-download-status="<?php echo $download_status; ?>"
										   data-unit-category='<?php echo join( ',', $download_unit_categories ); ?>'
										   data-progress='<?php echo $download_progress; ?>'
										   data-location-name='<?php echo $state_data->name; ?>'
										   data-unit-id='<?php echo $state_data->id; ?>'
										   data-api-status='<?php echo $api_status; ?>'
										>
											<?php echo __( 'Download', 'rvn' ) . ' <b>' . esc_attr( $state_data->name ) . '</b>'; ?>
										</a>
										<!-- State Link -->
										<span data-unit-id='<?php echo $state_data->id; ?>'
										      class="js-issslpg-state-link-wrapper  issslpg-nowrap  <?php echo $link_visibility_class; ?>">
											<a class="js-issslpg-state-link  issslpg-state-link"
											   href="<?php echo add_query_arg( 'state_id', $state_data->id ); ?>"
											>
												<?php echo esc_attr( $state_data->name ); ?>
											</a>
											-
											<a href="<?php echo admin_url( "admin.php?page={$this->plugin_id}-edit-state&state_id={$state_data->id}" ); ?>">
												Edit
											</a>
										</span>
									</td>
									<td>
										<!-- Progress Bar -->
										<div data-active="false"
										     data-unit-id="<?php echo $state_data->id; ?>"
										     data-status="<?php echo $download_status; ?>"
										     data-progress="<?php echo $download_progress; ?>"
										     class="js-issslpg-progress-bar  issslpg-progress-bar  <?php echo $hide_progress_bar_class; ?>"
										>
											<div class="issslpg-progress-bar-status-wrapper">
												<div style="width: <?php echo $download_progress; ?>%;" class="js-issslpg-progress-bar-status  issslpg-progress-bar-status"></div>
											</div>
										</div>
									</td>
									<!--
									<td>
										<?php echo esc_attr( $state_data->phone ); ?>
									</td>
									-->
								</tr>
							<?php endforeach; ?>
						<?php endforeach; ?>
					</tbody>
				</table>
				<?php //submit_button(); ?>
			</form>
		</div><!-- .wrap -->
		<?php
	}

	private function output_counties_settings_page( $state_id ) {

		$state = State::where( 'id', $state_id )->first();
		if ( ! $state ) {
			return false;
		}

		$counties = $state->counties;

		// If we want to save
		$saved = false;
		if ( ( isset( $_GET['save'] ) && $_GET['save'] == 'true' )
		     && isset( $_POST['issslpg_location_options'] ) ) {
			$saved = $this->save_counties_settings_page();
		}

		// Output:
		?>
		<div class="wrap">
			<h1><?php echo 'Counties in ' . esc_attr( $state->name ) ?></h1>
			<?php $this->output_breadcrumbs( $state->country, $state ); ?>

			<?php if ( $saved ) : ?>
				<div id="message" class="updated notice notice-success is-dismissible">
					<p>Counties updated.</p>
					<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
				</div>
			<?php endif; ?>

			<form method="post" action="<?php echo admin_url( "admin.php?page=issslpg_location_settings&state_id={$state->id}&save=true" ); ?>">
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<!--
							<td id="cb" class="manage-column column-cb check-column">
								<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
								<input id="cb-select-all-1" type="checkbox">
							</td>
							-->
							<td id="cb" class="manage-column column-cb check-column">
							</td>
							<th scope="col" id="title" class="column-title column-primary">
								<span>County Name</span>
							</th>
							<td>
								Phone
							</td>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $counties as $county ) : ?>
							<?php
							$county_data = new ISSSLPG_County_Data( $county );
							?>
							<tr valign="top">
								<th scope="row" class="issslpg-check-column  check-column">
									<!-- <input onclick="return false;" type="checkbox" name="<?php echo $this->options_handle ?>[active_counties][<?php echo $county->id; ?>]" <?php checked( $county_data->status ); ?>> -->
									<!-- <input type="hidden" name="<?php echo $this->options_handle ?>[inactive_counties][<?php echo $county->id; ?>]" value="0"> -->
									<?php if ( $county_data->status ) : ?>
										<!-- <span class="dashicons dashicons-yes"></span> -->
										<span class="issslpg-active-indicator">&#9679;</span>
									<?php endif; ?>
								</th>
								<td>
									<a href="<?php echo add_query_arg( 'county_id', $county_data->id ); ?>">
										<?php echo esc_attr( $county->name ); ?>
									</a>
									-
									<a href="<?php echo admin_url( "admin.php?page={$this->plugin_id}-edit-county&county_id={$county_data->id}" ); ?>">
										Edit
									</a>
								</td>
								<td>
									<?php echo esc_attr( $county_data->phone ); ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<?php //submit_button(); ?>
			</form>
		</div><!-- .wrap -->
		<?php
	}

	private function output_cities_settings_page( $county_id ) {

		$county = County::where( 'id', $county_id )->first();
		if ( ! $county ) {
			return false;
		}

		$cities  = $county->cities;
		$state   = $county->state;
		$country = $county->state->country;

		// Paginator
		$paginator_total_item_count = count( $cities );
		$paginator_current_page_number = isset( $_GET['current_page'] ) ? intval( $_GET['current_page'] ) : 1;
		$paginator_offset = 100;
		$paginator_page_count = ceil( $paginator_total_item_count / $paginator_offset );
		$paginator_item_offset = $paginator_current_page_number * $paginator_offset;
		$paginator_current_offset = $paginator_item_offset - $paginator_offset;

		// If we want to save
		$update_code = 0;
		if ( ( isset( $_GET['save'] ) && $_GET['save'] == 'true' )
		     && isset( $_POST['issslpg_location_options'] ) ) {
			$update_code = $this->save_cities_settings_page();
		}

		// Output:
		?>
		<div class="wrap">

			<h1><?php echo 'Cities in ' . esc_attr( $county->name ) . ', ' . esc_attr( $state->name ); ?></h1>
			<?php echo $this->output_breadcrumbs( $country, $state, $county ); ?>

			<?php if ( $update_code == -1 ) : ?>
				<?php ISSSLPG_Admin_Notices::display_county_limit_reached_notice(); ?>
			<?php elseif ( $update_code == 1 ) : ?>
				<?php echo ISSSLPG_Admin_Notices::create_notice( 'Cities updated.', 'notice-success' ); ?>
			<?php endif; ?>

			<form method="post" action="<?php echo admin_url( "admin.php?page=issslpg_location_settings&state_id={$county->state->id}&county_id={$county->id}&current_page={$paginator_current_page_number}&save=true" ); ?>">
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<td id="cb" class="column-cb check-column">
								<input type="checkbox">
							</td>
							<th scope="col" id="title" class="column-title column-primary">
								<span>City Name</span>
							</th>
							<th>
								Phone
							</th>
						</tr>
					</thead>
					<tbody>
						<?php //foreach ( $cities as $city ) : ?>
						<?php for ( $i = $paginator_current_offset; $i < $paginator_item_offset; $i++ ) : ?>
							<?php
							if ( ! isset( $cities[$i] ) ) {
								continue;
							}
							$city = $cities[$i];
							$city_data = new ISSSLPG_City_Data( $city );

							// Check if city landing page is scheduled to update
							global $wpdb;
							$results = $wpdb->get_results( "
								SELECT active
								FROM {$wpdb->prefix}issslpg_scheduled_landing_page_updates
								WHERE city_id = {$city_data->id}
								LIMIT 1
							" );

							// If city landing page is scheduled to update, display set the scheduled status
							$active = $city_data->status;
							if ( isset( $results[0] ) && is_object( $results[0] ) ) {
								$active = $results[0]->active;
							}

							?>
							<tr valign="top">
								<th scope="row" class="check-column">
									<input type="checkbox" name="<?php echo $this->options_handle; ?>[active_cities][<?php echo $city_data->id; ?>]" <?php checked( $active ); ?>>
									<input type="hidden" name="<?php echo $this->options_handle; ?>[inactive_cities][<?php echo $city_data->id; ?>]" value="0">
								</th>
								<td>
									<?php echo esc_attr( $city_data->name ); ?>
									-
									<a href="<?php echo admin_url( "admin.php?page={$this->plugin_id}-edit-city&county_id={$county->id}&city_id={$city_data->id}" ); ?>">
										Edit
									</a>
								</td>
								<td>
									<?php echo esc_attr( $city_data->phone ); ?>
								</td>
							</tr>
						<?php endfor; ?>
						<?php //endforeach; ?>
					</tbody>
				</table>
				<?php if ( $paginator_current_page_number != 1 || $paginator_current_page_number < $paginator_page_count ) : ?>
					<p class="issslpg-paginator">
						<?php
						if ( $paginator_current_page_number != 1 ) :
							$next_page = $paginator_current_page_number - 1;
							$url = admin_url( "admin.php?page=issslpg_location_settings&state_id={$county->state->id}&county_id={$county->id}&current_page={$next_page}" );
							echo "<a class='button button-secondary' href='$url'>&laquo; Prev Page</a> ";
						endif;
						if ( $paginator_current_page_number < $paginator_page_count ) :
							$next_page = $paginator_current_page_number + 1;
							$url = admin_url( "admin.php?page=issslpg_location_settings&state_id={$county->state->id}&county_id={$county->id}&current_page={$next_page}" );
							echo "<a class='button button-secondary' href='$url'>Next Page &raquo;</a>";
						endif;
						?>
					</p>
				<?php endif; ?>
				<?php submit_button(); ?>
			</form>
		</div><!-- .wrap -->
		<?php
	}

	public function output_edit_state_form() {

		// If we want to save
		$saved = false;
		if ( ( isset( $_GET['save'] ) && $_GET['save'] == 'true' )
		     && isset( $_POST['issslpg_location_options'] ) ) {
			$saved = $this->save_state_form();
		}

		// Get state data or fail
		$state_data = false;
		if ( isset( $_GET['state_id'] ) ) {
			$state_data = new ISSSLPG_State_Data( intval( $_GET['state_id'] ) );
		}
		if ( ! $state_data ) {
			return false;
		}

		// Office Google PID
		$office_google_pid = $state_data->office_google_pid;
		?>
		<div class="wrap">
			<h1>Edit <?php echo $state_data->name; ?></h1>
			<?php echo $this->output_breadcrumbs( $state_data->country_object, $state_data->state_object ); ?>

			<?php if ( $saved ) : ?>
				<div id="message" class="updated notice notice-success is-dismissible">
					<p>
						State updated.
					</p>
					<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
				</div>
			<?php endif; ?>

			<form method="post" action="<?php echo admin_url( "admin.php?page=issslpg-edit-state&state_id={$state_data->id}&save=true" ); ?>">

				<table class="form-table">

					<!-- OFFICE ADDRESS -->
					<tr valign="top">
						<th scope="row">
							<?php esc_html_e( 'Google Place ID', 'issslpg' ); ?>
						</th>
						<td>
							<p>
								<input placeholder="<?php _e( 'Google Place ID') ?>" type="text" name="<?php echo $this->options_handle ?>[office_google_pid]" value="<?php echo esc_attr( $office_google_pid ); ?>">
							</p>
							<p>
								Enter the <a href='https://developers.google.com/places/place-id' target='_blank'>Google Place ID</a> of the main office location in this state.
								This location will be used as a fallback, when no closer office location is set on the county level, when you're displaying the Directions Map shortcode <code>[iss_directions_map]</code> or widget.
							</p>
						</td>
					<td>

					<!-- ACTIVE -->
					<!--
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Active', 'issslpg' ); ?></th>
						<td>
							<input type="checkbox" name="<?php echo $this->options_handle ?>[active]" <?php checked( $state_data->status ); ?>>
							<?php esc_html_e( 'Activate State', 'issslpg' ); ?>
						</td>
					</tr>
					-->

					<!-- PHONE -->
					<!--
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Phone', 'issslpg' ); ?></th>
						<td>
							<input type="text" name="<?php echo $this->options_handle ?>[phone]" value="<?php echo esc_attr( $state_data->phone ); ?>">
						</td>
					</tr>
					-->

					<input type="hidden" name="<?php echo $this->options_handle ?>[state_id]" value="<?php echo esc_attr( $state_data->id ); ?>">

				</table>

				<?php submit_button(); ?>

			</form>

		</div><!-- .wrap -->
		<?php
	}

	public function output_edit_county_form() {

		// If we want to save
		$saved = false;
		if ( ( isset( $_GET['save'] ) && $_GET['save'] == 'true' )
		     && isset( $_POST['issslpg_location_options'] ) ) {
			$saved = $this->save_county_form();
		}

		// Get county data or fail
		$county_data = false;
		if ( isset( $_GET['county_id'] ) ) {
			$county_data = new ISSSLPG_County_Data( intval( $_GET['county_id'] ) );
		}
		if ( ! $county_data ) {
			return false;
		}

		// Office Google PID
		$office_google_pid = $county_data->office_google_pid;

		// Get custom locations
		$custom_locations = $county_data->custom_locations;
		$custom_locations_text = $custom_locations ? json_encode( $custom_locations ) : '';
		?>
		<div class="wrap">

			<h1>Edit <?php echo $county_data->name; ?></h1>

			<?php echo $this->output_breadcrumbs( $county_data->country_object, $county_data->state_object, $county_data->county_object ); ?>

			<?php if ( $saved ) : ?>
				<div id="message" class="updated notice notice-success is-dismissible">
					<p>County updated.</p>
					<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
				</div>
			<?php endif; ?>

			<form method="post" enctype="multipart/form-data" action="<?php echo admin_url( "admin.php?page=issslpg-edit-county&county_id={$county_data->id}&save=true" ); ?>">

				<table class="isssplg-edit-county-form  form-table">

					<!-- ACTIVE -->
					<!--
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Active', 'issslpg' ); ?></th>
						<td>
							<input type="checkbox" name="<?php echo $this->options_handle ?>[active]" <?php checked( $county_data->status ); ?>>
							<?php esc_html_e( 'Activate County', 'issslpg' ); ?>
						</td>
					</tr>
					-->

					<!-- Local Office -->
					<tr valign="top">
						<th scope="row" colspan="2">
							<h3 style="margin-bottom: 0;">
								<?php _e( 'Local Office', 'issslpg' ); ?>
							</h3>
						</th>
					</tr>
					<!-- Phone -->
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Phone', 'issslpg' ); ?></th>
						<td>
							<input placeholder="<?php echo $county_data->inherited_phone; ?>" type="text" name="<?php echo $this->options_handle ?>[phone]" value="<?php echo esc_attr( $county_data->phone ); ?>">
						</td>
					</tr>
					<!-- Street -->
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Street', 'issslpg' ); ?></th>
						<td>
							<input type="text" name="<?php echo $this->options_handle ?>[office_street]" value="<?php echo esc_attr( $county_data->get_setting( 'office_street' ) ); ?>">
						</td>
					</tr>
					<!-- City -->
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'City', 'issslpg' ); ?></th>
						<td>
							<input type="text" name="<?php echo $this->options_handle ?>[office_city]" value="<?php echo esc_attr( $county_data->get_setting( 'office_city' ) ); ?>">
						</td>
					</tr>
					<!-- ZIP Code -->
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'ZIP Code', 'issslpg' ); ?></th>
						<td>
							<input type="text" name="<?php echo $this->options_handle ?>[office_zip_code]" value="<?php echo esc_attr( $county_data->get_setting( 'office_zip_code' ) ); ?>">
						</td>
					</tr>
					<!-- Google Place ID -->
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Google Place ID', 'issslpg' ); ?></th>
						<td>
							<p>
								<input placeholder="<?php _e( 'Google Place ID') ?>" type="text" name="<?php echo $this->options_handle ?>[office_google_pid]" value="<?php echo esc_attr( $office_google_pid ); ?>">
							</p>
							<p>
								Enter the <a href='https://developers.google.com/places/place-id' target='_blank'>Google Place ID</a> of the office location in this county.
								This ID will be used to display the Directions Map shortcode <code>[iss_directions_map]</code> or widget.
							</p>
						</td>
					<td>

					<!-- CUSTOM LOCATIONS INTERFACE -->
					<tr valign="top">
						<th scope="row" colspan="2">
							<hr>
							<h3 style="margin-bottom: 0;">
								<?php _e( 'Custom Locations', 'issslpg' ); ?>
							</h3>
						</th>
					</tr>
					<tr valign="top" class="isssplg-custom-location-area  js-isssplg-custom-location-area">
						<th scope="row"><?php esc_html_e( 'Custom Locations', 'issslpg' ); ?></th>
						<td class="isssplg-custom-location-input-area  js-isssplg-custom-location-input-area  isssplg-custom-location-input-area--template  js-isssplg-custom-location-input-area-template">
							<div class="isssplg-custom-location-input-wrapper  js-isssplg-custom-location-input-wrapper">
								<input type="hidden" class="issslpg-custom-location-hash  js-issslpg-custom-location-hash" value="">
								<input type="hidden" class="issslpg-custom-location-method  js-issslpg-custom-location-method" value="">
								<input placeholder="Location Name" type="text" class="issslpg-custom-location-name" value="">
							</div>
							<div class="isssplg-custom-location-input-wrapper  js-isssplg-custom-location-input-wrapper">
								<input placeholder="Zip Codes" type="text" class="issslpg-custom-location-zip-codes" value="">
							</div>
							<div class="isssplg-custom-location-input-wrapper  js-isssplg-custom-location-input-wrapper">
								<input placeholder="Phone" type="text" class="issslpg-custom-location-phone" value="">
							</div>
							<div class="isssplg-custom-location-controls">
								<div class="isssplg-custom-location-controls">
									<div class="isssplg-custom-location-remove-button  js-isssplg-custom-location-remove-button">
										<div>&ndash;</div>
									</div>
									<div class="isssplg-custom-location-add-button  js-isssplg-custom-location-add-button">
										<div>+</div>
									</div>
								</div>
							</div>
						</td>
						<?php $custom_locations = $county_data->custom_locations; ?>
						<?php if ( ! empty( $custom_locations ) && is_array( $custom_locations ) ) : ?>
							<?php foreach ( $custom_locations as $custom_location ) : ?>
								<?php
								$hash      = empty( $custom_location['hash'] ) ? '' : $custom_location['hash'];
								$method    = empty( $custom_location['method'] ) ? '' : $custom_location['method'];
								$name      = empty( $custom_location['name'] ) ? '' : $custom_location['name'];
								$zip_codes = empty( $custom_location['zip_codes'] ) ? '' : join( ', ', $custom_location['zip_codes'] );
								$phone     = empty( $custom_location['phone'] ) ? '' : $custom_location['phone'];
								?>
								<td class="isssplg-custom-location-input-area  js-isssplg-custom-location-input-area">
									<div class="isssplg-custom-location-input-wrapper  js-isssplg-custom-location-input-wrapper">
										<input type="hidden" class="issslpg-custom-location-hash  js-issslpg-custom-location-hash" value="<?php echo $hash; ?>">
										<input type="hidden" class="issslpg-custom-location-method  js-issslpg-custom-location-method" value="<?php echo $method; ?>">
										<input placeholder="Location Name" type="text" class="issslpg-custom-location-name" value="<?php echo $name; ?>">
									</div>
									<div class="isssplg-custom-location-input-wrapper  js-isssplg-custom-location-input-wrapper">
										<input placeholder="Zip Codes" type="text" class="issslpg-custom-location-zip-codes" value="<?php echo $zip_codes; ?>">
									</div>
									<div class="isssplg-custom-location-input-wrapper  js-isssplg-custom-location-input-wrapper">
										<input placeholder="Phone" type="text" class="issslpg-custom-location-phone" value="<?php echo $phone; ?>">
									</div>
									<div class="isssplg-custom-location-controls">
										<div class="isssplg-custom-location-remove-button  js-isssplg-custom-location-remove-button">
											<div>&ndash;</div>
										</div>
										<div class="isssplg-custom-location-add-button  js-isssplg-custom-location-add-button">
											<div>+</div>
										</div>
									</div>
								</td>
							<?php endforeach; ?>
						<?php endif; ?>
						<textarea style="display:none;" class="js-isssplg-custom-location-data" name="<?php echo $this->options_handle ?>[custom_locations]" cols="180" rows="12"><?php echo $custom_locations_text; ?></textarea>
					</tr>
					<tr valign="top">
						<th scope="row"></th>
						<td>
							<div class="isssplg-custom-location-import-export-area">
								<div class="isssplg-custom-location-import-area">
									<a href="<?php echo basename( $_SERVER['REQUEST_URI'] ); ?>&export_custom_locations=true" class="js-isssplg-custom-location-export-button  button  button-primary">
										<span class="dashicons dashicons-download" style="margin-top: 4px;"></span>
										Export Custom Locations
									</a>
								</div>
								<div class="isssplg-custom-location-export-area">
									<label for="isssplg-custom-location-import">Import: </label>
									<input type="file" name="custom_location_import_file" class="isssplg-custom-location-import">
								</div>
							</div>
						</td>
					</tr>

					<!-- CUSTOM LOCATIONS EXPORTER -->
					<tr valign="top">
						<th scope="row" colspan="2">
							<hr>
							<h3 style="margin-bottom: 0;">
								<?php _e( 'Template Pages', 'issslpg' ); ?>
							</h3>
						</th>
					</tr>
					<!-- EXCLUDE TEMPLATE PAGES -->
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Exclude Template Pages', 'issslpg' ); ?></th>
						<td>
							<?php
							global $wpdb;
							$template_pages = new WP_Query( array(
								'post_type'      => 'issslpg-template',
								'post_status'    => 'publish',
								'posts_per_page' => -1,
							) );
							while ( $template_pages->have_posts() ) :
								$template_pages->the_post();
								$template_pages_id = get_the_ID();
								$result = $wpdb->get_row( "
									SELECT template_page_id
									FROM {$wpdb->prefix}issslpg_excluded_county_template_pages
									WHERE county_id = {$county_data->id}
									AND template_page_id = {$template_pages_id}
								" );
								?>
								<label style="display: block; margin-top: .5rem">
									<input type='checkbox' name='<?php echo "{$this->options_handle}[exclude_template_page_{$template_pages_id}]"; ?>' <?php checked( isset( $result ) ); ?> >
									<?php the_title(); ?>
								</label>
								<?php
							endwhile;
							wp_reset_postdata();
							?>
						</td>
					</tr>

					<input type="hidden" name="<?php echo $this->options_handle ?>[county_id]" value="<?php echo esc_attr( $county_data->id ); ?>">

				</table>

				<?php submit_button(); ?>

			</form>

		</div><!-- .wrap -->
		<?php
	}

	public function output_edit_city_form() {

		// If we want to save the form
		$update_code = 0;
		if ( ( isset( $_GET['save'] ) && $_GET['save'] == 'true' )
		     && isset( $_POST['issslpg_location_options'] ) ) {
			$update_code = $this->save_city_form();
		}

		// Get city data
		$city_data = false;
		if ( isset( $_GET['city_id'] ) ) {
			$city_data = new ISSSLPG_City_Data( intval( $_GET['city_id'] ) );
		}

		// Get county data
		$county_data = false;
		if ( isset( $_GET['county_id'] ) ) {
			$county_data = new ISSSLPG_County_Data( intval( $_GET['county_id'] ) );
		}

		// Fail if city or county are missing
		if ( ! $city_data || ! $county_data ) {
			return false;
		}

		// Check if city landing page is scheduled to update
		global $wpdb;
		$results = $wpdb->get_results( "
			SELECT active
			FROM {$wpdb->prefix}issslpg_scheduled_landing_page_updates
			WHERE city_id = {$city_data->id}
			AND method = 'create'
			LIMIT 1
		" );

		// If city landing page is scheduled to update, display set the scheduled status
		$active = $city_data->status;
		if ( isset( $results[0] ) && is_object( $results[0] ) ) {
			$active = $results[0]->active;
		}
		?>
		<div class="wrap">

			<h1>Edit <?php echo $city_data->name; ?>, <?php echo $city_data->state; ?></h1>

			<?php echo $this->output_breadcrumbs( $city_data->country_object, $city_data->state_object, $county_data->county_object, $city_data->city_object ); ?>

			<?php if ( $update_code == -1 ) : ?>
				<?php ISSSLPG_Admin_Notices::display_county_limit_reached_notice(); ?>
			<?php elseif ( $update_code == 1 ) : ?>
				<?php echo ISSSLPG_Admin_Notices::create_notice( 'City updated.', 'notice-success' ); ?>
			<?php endif; ?>

			<form method="post" action="<?php echo admin_url( "admin.php?page=issslpg-edit-city&county_id={$county_data->id}&city_id={$city_data->id}&save=true" ); ?>">

				<table class="form-table">

					<!-- ACTIVE -->
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Active', 'issslpg' ); ?></th>
						<td>
							<input type="checkbox" name="<?php echo $this->options_handle ?>[active]" <?php checked( $active ); ?>>
							<?php esc_html_e( 'Activate City', 'issslpg' ); ?>
						</td>
					</tr>

					<!-- PHONE -->
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Phone', 'issslpg' ); ?></th>
						<td>
							<input type="text" name="<?php echo $this->options_handle ?>[phone]" value="<?php echo esc_attr( $city_data->phone ); ?>">
						</td>
					</tr>

					<input type="hidden" name="<?php echo $this->options_handle ?>[city_id]" value="<?php echo esc_attr( $city_data->id ); ?>">

				</table>

				<?php submit_button(); ?>

			</form>

		</div><!-- .wrap -->
		<?php
	}

	public function save_states_settings_page() {

		$states = State::all();

		if ( ! $states ) {
			return false;
		}

		$state_updated = false;

		foreach ( $states as $state ) {
			$state_data = new ISSSLPG_State_Data( $state );

			$active = false;
			if ( isset( $_POST['issslpg_location_options']['active_states'] ) ) {
				$active_states = array_map( 'sanitize_text_field', wp_unslash( $_POST['issslpg_location_options']['active_states'] ) );
				if ( array_key_exists( $state_data->id, $active_states ) ) {
					$active = true;
				}
			}

			// Only update county, if status has actually changed
			if ( $state_data->status !== $active ) {
				$state_updated = $state_data->update( array( 'active' => $active ) );
			}
		}

		if ( $state_updated ) {
			return true;
		}

		return false;
	}

	public function save_counties_settings_page() {

		if ( ! isset( $_GET['state_id'] ) ) {
			return false;
		}

		$state_data = new ISSSLPG_State_Data( intval( $_GET['state_id'] ) );

		if ( ! $state_data ) {
			return false;
		}

		$counties       = $state_data->get_counties_object();
		$county_updated = false;

		foreach ( $counties as $county ) {
			$county_data = new ISSSLPG_County_Data( $county );

			$active = false;
			if ( isset( $_POST['issslpg_location_options']['active_counties'] ) ) {
				$active_counties = array_map( 'sanitize_text_field', wp_unslash( $_POST['issslpg_location_options']['active_counties'] ) );
				if ( array_key_exists( $county_data->id, $active_counties ) ) {
					$active = true;
				}
			}

			// Only update county, if status has actually changed
			if ( $county_data->status !== $active ) {
				$county_updated = $county_data->update( array( 'active' => $active ) );
			}
		}

		if ( $county_updated ) {
			return true;
		}

		return false;
	}

	public function save_cities_settings_page() {

		if ( ! isset( $_GET['county_id'] ) ) {
			return false;
		}

		$county_data = new ISSSLPG_County_Data( intval( $_GET['county_id'] ) );
		if ( ! $county_data ) {
			return false;
		}

		$update_code  = 0;
		$cities       = $county_data->get_cities_object();

		foreach ( $cities as $city ) {
			$city_data = new ISSSLPG_City_Data( $city );

			$active = false;
			if ( isset( $_POST['issslpg_location_options']['active_cities'] ) ) {
				$active_cities = array_map( 'sanitize_text_field', wp_unslash( $_POST['issslpg_location_options']['active_cities'] ) );
				if ( array_key_exists( $city_data->id, $active_cities ) ) {
					$active = true;
				}
			}

			// Only update city, if status has actually changed
//			if ( $city_data->status !== $active ) {
//				$update_code = $city_data->update(
//					array( 'active' => $active ),
//					$county_data->county_object
//				);
//				if ( $update_code === -1 ) {
//					return $update_code;
//				}
//			}
			if ( $city_data->status !== $active ) {
				ISSSLPG_Admin_Scheduled_Tasks::add_landing_pages_to_activate( $city_data->id, $county_data->id, $active );
//				global $wpdb;
//				$wpdb->replace(
//						"{$wpdb->prefix}issslpg_scheduled_landing_page_updates",
//						array( 'city_id'   => $city_data->id,
//						       'county_id' => $county_data->id,
//						       'active'    => $active,
//						       'method'    => 'create',
//						),
//						array( '%d', '%d', '%d', '%s' )
//				);
			}

		}

		return $update_code;
	}

	public function save_state_form() {
		if ( ! isset( $_POST['issslpg_location_options']['state_id'] ) ) {
			return false;
		}
		$options    = array_map( 'sanitize_text_field', wp_unslash( $_POST['issslpg_location_options'] ) );
		$state_data = new ISSSLPG_State_Data( intval( $options['state_id'] ) );
		if ( ! $state_data ) {
			return false;
		}

		// $active = isset( $_POST['issslpg_location_options']['active'] ) ? true : false;
		$office_google_pid = empty( $options['office_google_pid'] ) ? null : $options['office_google_pid'];
//				$phone  = isset( $_POST['issslpg_location_options']['phone'] ) ? $_POST['issslpg_location_options']['phone'] : false;
		$state_updated = $state_data->update( array(
			// 'active' => $active,
			'office_google_pid' => $office_google_pid,
			//'phone'  => $phone,
		) );
		if ( $state_updated ) {
			return true;
		}
	}

	public function save_county_form() {
		if ( ! isset( $_POST['issslpg_location_options']['county_id'] ) ) {
			return false;
		}

		global $wpdb;
		$options     = array_map( 'sanitize_text_field', wp_unslash( $_POST['issslpg_location_options'] ) );
		$county_data = new ISSSLPG_County_Data( intval( $options['county_id'] ) );

		if ( ! $county_data ) {
			return false;
		}

		// Custom Location File Import
		$custom_locations_from_import_file = false;
		if ( ! empty( $_FILES['custom_location_import_file']['tmp_name'] ) ) {
			if ( 'text/csv' != $_FILES['custom_location_import_file']['type'] ) {
				wp_die( 'Import file has to be in CSV format.', 'Wrong file format' );
			}
			if ( $fp = fopen( $_FILES['custom_location_import_file']['tmp_name'], 'r' ) ) {
				// Read CSV headers
				$keys = fgetcsv( $fp, "5120", "," );
				$location_data = array();
				while ( $row = fgetcsv( $fp, "5120", "," ) ) {
					$json_row = array_combine( $keys, $row );
					if ( empty( $json_row['name'] ) ) {
						continue;
					}
					if ( empty( $json_row['method'] ) ) {
						$json_row['method'] = 'add';
					}
					if ( empty( $json_row['hash'] ) ) {
						$rand_num = rand( 1000, 10000000 );
						$hash = hash( 'sha256', $rand_num );
						$json_row['hash'] = substr( $hash, 0, 12 );
					}
					if ( ! empty( $json_row['zip_codes'] ) ) {
						$json_row['zip_codes'] = array_map( 'trim', explode( ',', $json_row['zip_codes'] ) );
					}
					$location_data[] = $json_row;
					$custom_locations_from_import_file = $location_data;
				}
				fclose( $fp );
			}
//			$file_content = file_get_contents( $_FILES['custom_location_import_file']['tmp_name'] );
//			$custom_locations_from_import_file = json_decode( $file_content, true );
		}

		$template_pages = new WP_Query( array(
			'post_type'      => 'issslpg-template',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		) );
		while ( $template_pages->have_posts() ) :
			$template_pages->the_post();
			$template_pages_id = get_the_ID();
			if ( isset( $options["exclude_template_page_{$template_pages_id}"] ) ) {
				$result = $wpdb->get_row( "
					SELECT template_page_id
					FROM {$wpdb->prefix}issslpg_excluded_county_template_pages
					WHERE county_id = {$options['county_id']}
					AND template_page_id = {$template_pages_id}
				" );
				if ( ! $result ) {
					$wpdb->replace(
						"{$wpdb->prefix}issslpg_excluded_county_template_pages",
						array(
							'county_id' => $options['county_id'],
							'template_page_id' => $template_pages_id
						),
						array( '%d', '%d' )
					);
				}
			}
			else {
				$wpdb->delete(
					"{$wpdb->prefix}issslpg_excluded_county_template_pages",
					array(
						'county_id' => $options['county_id'],
						'template_page_id' => $template_pages_id
					),
					array( '%d', '%d' )
				);
			}
		endwhile;
		wp_reset_postdata();

//				$active = isset( $_POST['issslpg_location_options']['active'] ) ? true : false;
		$phone = isset( $options['phone'] ) ? sanitize_text_field( $options['phone'] ) : false;
		$office_google_pid = empty( $options['office_google_pid'] ) ? null : $options['office_google_pid'];

		if ( $custom_locations_from_import_file ) {
			$custom_locations = $custom_locations_from_import_file;
		} else {
			$custom_locations = isset( $_POST['issslpg_location_options']['custom_locations'] ) ? $_POST['issslpg_location_options']['custom_locations'] : false;
			$custom_locations = json_decode( wp_unslash( $custom_locations ), true );
		}

		// Schedule
		if ( $custom_locations ) {
			foreach ( $custom_locations as $key => $custom_location ) {
				if ( ! empty( $custom_location['name'] ) ) {
					$status = ( $custom_location['method'] == 'add' ) ? '1' : '0';
					ISSSLPG_Admin_Scheduled_Tasks::add_custom_location_landing_pages( $county_data->id, $custom_location['hash'], $status );
					if ( isset( $custom_location['method'] ) && $custom_location['method'] != 'add' ) {
						unset( $custom_locations[ $key ] );
					}
				}
			}
		}

		// Update County Data
		$county_updated = $county_data->update( array(
			// 'active' => $active,
			'phone' => $phone,
			'office_google_pid' => $office_google_pid,
			'custom_locations' => ( $custom_locations ? serialize( $custom_locations ) : null ),
			'settings' => serialize( array(
				'office_street' => isset( $options['office_street'] ) ? $options['office_street'] : null,
				'office_city' => isset( $options['office_city'] ) ? $options['office_city'] : null,
				'office_zip_code' => isset( $options['office_zip_code'] ) ? $options['office_zip_code'] : null,
			) ),
		) );

		ISSSLPG_Landing_Page::delete_excluded_landing_pages_by_county( $options['county_id'] );
		ISSSLPG_Landing_Page::untrash_landing_pages_by_county( $options['county_id'] );

		if ( $county_updated ) {
			return true;
		}
	}

	public function save_city_form() {
		$update_code = 0;

		if ( isset( $_POST['issslpg_location_options']['city_id'] ) && isset( $_GET['county_id'] ) ) {
			$options     = array_map( 'sanitize_text_field', wp_unslash( $_POST['issslpg_location_options'] ) );
			$county_data = new ISSSLPG_County_Data( intval( $_GET['county_id'] ) );
			$city_data   = new ISSSLPG_City_Data( intval( $options['city_id'] ) );

			if ( $city_data && $county_data ) {
				$active = isset( $options['active'] ) ? '1' : '0';
				$phone  = isset( $options['phone'] )  ? $options['phone'] : false;

				$update_code = $city_data->update(
					array(
						'active' => $active,
						'phone'  => $phone,
					),
					$county_data->county_object
				);
			}
		}

		return $update_code;
	}

}