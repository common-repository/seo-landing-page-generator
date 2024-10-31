<?php



class ISSSLPG_Admin_Notices {

	static public function create_notice( $text, $status_class = 'notice-info', $dismissible = true ) {
		$dismissible_class = $dismissible ? 'is-dismissible' : '';

		$output = "<div class='notice {$status_class} {$dismissible_class}'>";
		$output.= "<p>{$text}</p>";
		if ( $dismissible ) {
			$output.= "<button type='button' class='notice-dismiss'><span class='screen-reader-text'>Dismiss this notice.</span></button>";
		}
		$output.= "</div>";

		return $output;
	}

	static public function display_county_limit_reached_notice() {
		if ( ! ISSSLPG_Helpers::is_plan( 'pro' ) || ISSSLPG_Helpers::is_white_labeled() ) {
			return;
		}

		$county_limit = ISSSLPG_Helpers::get_county_limit();
		$text         = "You're using the <b>Pro</b> version of the SEO Landing Page Generator and are only allowed to activate <b>{$county_limit}</b> counties.";
		if ( ! ISSSLPG_Helpers::is_simulated_plan() ) {
			$upgrade_url = issslpg_fs()->get_upgrade_url();
			$text.= " Consider <a href='{$upgrade_url}'>upgrading your plan</a> to be able to add more.";
		}
		echo self::create_notice( $text, 'notice-warning' );
	}

	static public function debug_notice() {
		if ( ! isset( $_GET['page'] ) ) {
			return;
		}
		if ( 'iss_debug_settings' != $_GET['page'] ) {
			return;
		}

		$text = __( 'This panel is for debug purposes only. Please use these options with care as they can interrupt landing page creation.', 'issslpg' );
		echo self::create_notice( $text, 'notice-warning', false );
	}

	static public function documentation_notice() {
		if ( ISSSLPG_Helpers::is_white_labeled() ) {
			return;
		}
		$data = self::get_doc_video_data();
		if ( ! $data ) {
			return;
		}
		?>
		<style>
			.issslpg-documentation-notice {
				padding: 1.5rem 1.5rem;
				display: flex;
			}
            .issslpg-documentation-notice-heading {
                margin-top: .7rem;
            }
            .issslpg-documentation-notice img {
                max-width: 200px;
                transition: all .2s ease-in-out;
            }
            .issslpg-documentation-notice a {
                font-weight: 600;
                text-decoration: none;
            }
            .issslpg-documentation-notice a:hover {
                text-decoration: underline;
            }
            .issslpg-documentation-notice img:hover {
                transform: scale(1.04);
            }
            .issslpg-documentation-notice-col-2 {
                padding-left: 1.5rem;
            }
            .issslpg-documentation-notice-text {
                font-size: .95rem;
                line-height: 1.6;
            }
            .issslpg-documentation-notice-new-tab-link {
                display: block;
                text-align: center;
            }
		</style>
		<div class='notice issslpg-documentation-notice'>
			<div class="issslpg-documentation-notice-col-1 js-issslpg-lightgallery">
				<?php for ( $i = 0; $i < count( $data['videos'] ); $i++ ) : ?>
					<?php if ( $i === 0 ) : ?>
						<div>
							<a class="js-issslpg-lightgallery-item" href="<?php echo $data['videos'][$i]; ?>">
								<img src="<?php echo plugin_dir_url( __DIR__ ) . 'admin/images/how-to-video-preview.jpg' ; ?>" alt="">
							</a>
						</div>
						<a class="issslpg-documentation-notice-new-tab-link" href="<?php echo $data['videos'][$i]; ?>" target="_blank">
							Open video in new tab
						</a>
					<?php else : ?>
						<a class="js-issslpg-lightgallery-item" style="display: none;" href="<?php echo $data['videos'][$i]; ?>">Video</a>
					<?php endif; ?>
				<?php endfor; ?>
			</div>
			<div class="issslpg-documentation-notice-col-2">
				<h2 class="issslpg-documentation-notice-heading">
					<?php echo $data['title']; ?>
				</h2>
				<div class="issslpg-documentation-notice-text">
					To get started with the SEO Landing Page Generator, please watch our
					<a href="https://intellasoftplugins.com/how-to-videos/" target="_blank">training videos</a> or read our <a href="<?php echo admin_url( 'admin.php?page=docs' ); ?>">documentation</a>.
					<br>
					If you have further questions, you can open a support ticket by sending an email to <a href="mailto:support@intellasoftplugins.com" target="_blank">support@intellasoftplugins.com</a> or call us directly at
					<a href="tel:877-764-6366">877-764-6366</a>.
				</div>
			</div>
		</div>
		<?php
	}

	static public function get_doc_video_data() {
		$cr_active = ISSSLPG_Helpers::is_content_randomizer_plugin_active();
		$current_screen = get_current_screen();
//		var_dump( $current_screen->id);
		switch ( $current_screen->id ) {
			case 'toplevel_page_issslpg_location_settings' :
				if ( $cr_active ) {
					return [
						'title'  => 'SEO Plugins: Setup',
						'videos' => [
							'https://youtu.be/Cd8QlxrrB28',
						],
					];
				} else {
					return [
						'title'  => 'Landing Page Generator: Setup',
						'videos' => [
							'https://youtu.be/VDaZTfcxgEA',
						],
					];
				}
			case 'edit-issslpg-template' :
			case 'issslpg-template' :
				if ( $cr_active ) {
					return [
						'title'  => 'SEO Plugins: Template Page Setup',
						'videos' => [
							'https://youtu.be/ertMPfAjrkk',
							'https://youtu.be/MBHU001NwYs',
							'https://youtu.be/vxCtyWD1fDI',
							'https://youtu.be/mFp3HerG6s0',
						],
					];
				} else {
					return [
						'title'  => 'Landing Page Generator: Setup',
						'videos' => [
							'https://youtu.be/VDaZTfcxgEA',
						],
					];
				}
			case 'seo-landing-page-generator_page_issslpg_settings' :
				return [
					'title'  => 'Landing Page Generator: Main Settings',
					'videos' => [
						'https://youtu.be/l8BKgXDdIcM',
					],
				];
			case 'seo-landing-page-generator_page_iss_company_info_settings' :
				return [
					'title'  => 'Landing Page Generator: Company Info Settings',
					'videos' => [
						'https://youtu.be/cNVMrVJlLHA',
					],
				];
			case 'seo-landing-page-generator_page_issslpg_xml_sitemap_settings' :
				return [
					'title'  => 'Landing Page Generator: XML Sitemap Settings',
					'videos' => [
						'https://youtu.be/LvGhGAmz0T4',
					],
				];
			case 'seo-landing-page-generator_page_issslpg_html_sitemap_settings' :
				return [
					'title'  => 'Landing Page Generator: HTML Sitemap Settings',
					'videos' => [
						'https://youtu.be/MG7lTPxC6rY',
					],
				];
			case 'seo-landing-page-generator_page_iss_schema_settings' :
				return [
					'title'  => 'Landing Page Generator: Schema Settings',
					'videos' => [
						'https://youtu.be/I6q1cyE8pLY',
					],
				];
			case 'seo-landing-page-generator_page_iss_faq_settings' :
				return [
					'title'  => 'Landing Page Generator: FAQ Settings',
					'videos' => [
						'https://youtu.be/dCEQLCov2zo',
					],
				];
			case 'admin_page_issslpg-edit-state' :
				return [
					'title'  => 'Landing Page Generator: Edit State',
					'videos' => [
						'https://youtu.be/mgp0aCXTOHM',
					],
				];
			case 'admin_page_issslpg-edit-county' :
				return [
					'title'  => 'Landing Page Generator: Edit County',
					'videos' => [
						'https://youtu.be/Y7TVJhCNWoo',
						'https://youtu.be/mFw2iPFeWzI',
					],
				];
			case 'admin_page_issslpg-edit-city' :
				return [
					'title'  => 'Landing Page Generator: Edit City',
					'videos' => [
						'https://youtu.be/DlhU22XVYC4',
					],
				];
			case 'seo-landing-page-generator_page_iss_debug_settings' :
				return [
					'title'  => 'Landing Page Generator: Debug Panel',
					'videos' => [
						'https://youtu.be/8HnHxsaPESc',
					],
				];
			case 'issslpg-landing-page' :
			case 'edit-issslpg-landing-page' :
				return [
					'title'  => 'Landing Page Generator: Edit Landing Pages',
					'videos' => [
						'https://youtu.be/iJqOt7SVDGE',
					],
				];
			case 'issslpg-local' :
			case 'edit-issslpg-local' :
				return [
					'title'  => 'Landing Page Generator: Local Content',
					'videos' => [
						'https://youtu.be/X3oZeuWXVRQ',
					],
				];
			case 'widgets' :
				if ( $cr_active ) {
					return [
						'title'  => 'SEO Plugins: Widgets',
						'videos' => [
							'https://youtu.be/Vk_uLHlIAOg',
						],
					];
				}
		}

		return false;
	}

//	static public function documentation_notice() {
//		if ( ! isset( $_GET['page'] ) || ISSSLPG_Helpers::is_white_labeled() ) {
//			return;
//		}
//
//		$on_location_settings_screen = ( $_GET['page'] === 'issslpg_location_settings' );
//		$on_settings_screen          = ( $_GET['page'] === 'issslpg_settings' );
//		$on_sitemap_settings_screen  = ( $_GET['page'] === 'issslpg_xml_sitemap_settings' );
//		if ( $on_location_settings_screen || $on_settings_screen || $on_sitemap_settings_screen ) {
//			$text = __( 'If you need help with the setup or configuration of the SEO Landing Page Generator, take a look at our <a href="https://intellasoftplugins.com/how-to-videos/" target="_blank">How-To videos</a>.', 'issslpg' );
//			echo self::create_notice( $text, 'notice-info', false );
//		}
//	}

	static public function edit_page_update_button_reminder() {
		$on_edit_screen = ( isset( $_GET['action'] ) && $_GET['action'] === 'edit' );
		if ( $on_edit_screen && ISSSLPG_Landing_Page::is_landing_page() ) {
			$text = __( 'Please make sure to hit the <b>Update</b> button before leaving the edit screen.', 'issslpg' );
			echo self::create_notice( $text, 'notice-info', false );
		}
	}

	static public function free_plan_content_block_limit_upgrade_notice() {
		if ( ISSSLPG_Helpers::is_white_labeled() ) {
			return;
		}

		$on_edit_screen = ( isset( $_GET['action'] ) && $_GET['action'] === 'edit' );
		$landing_page   = ISSSLPG_Landing_Page::is_landing_page();
//		$on_template_or_landing_page = ( ISSSLPG_Template_Page::is_template_page() || ISSSLPG_Landing_Page::is_landing_page() );
		if ( ISSSLPG_Helpers::is_plan( 'free' ) && $on_edit_screen && $landing_page ) {
			$rows_limit  = ISSSLPG_Helpers::get_repeater_box_rows_limit();
			$text = "You're using the <b>Free</b> version of the SEO Landing Page Generator and can only create up to {$rows_limit} content blocks.";
			if ( ! ISSSLPG_Helpers::is_simulated_plan() ) {
				$upgrade_url = issslpg_fs()->get_upgrade_url();
				$text.= " Consider <a href='{$upgrade_url}'>upgrading your plan</a> to be able to add more.";
			}
			echo self::create_notice( $text, 'notice-info' );
		}
	}

	static public function free_plan_county_limit_upgrade_notice() {
		if ( ISSSLPG_Helpers::is_white_labeled() ) {
			return;
		}

		$on_settings_screen = ( isset( $_GET['page'] ) && $_GET['page'] === 'issslpg_location_settings' );
		$on_template_page   = ISSSLPG_Template_Page::is_template_page();
		if ( ISSSLPG_Helpers::is_plan( 'free' ) && ( $on_settings_screen || $on_template_page ) ) {
			$county_limit = ISSSLPG_Helpers::get_county_limit();
			$text = "You're using the <b>Free</b> version of the SEO Landing Page Generator and are only allowed to activate <b>{$county_limit}</b> county.";
			if ( ! ISSSLPG_Helpers::is_simulated_plan() ) {
				$upgrade_url = issslpg_fs()->get_upgrade_url();
				$text.= " Consider <a href='{$upgrade_url}'>upgrading your plan</a> to be able to add more.";
			}
			echo self::create_notice( $text, 'notice-info' );
		}
	}

}