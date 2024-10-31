<?php



class ISSSLPG_ISSSCR_Sample_Content {

	static public function add_sample_service_template_page() {
		$cr_settings = get_option( 'issscr_settings' );

		if ( ! empty( $cr_settings['post_type_issslpg-template_content_panels'] )
		     || ! empty( $cr_settings['post_type_issslpg-template_image_panels'] )
		     || ! empty( $cr_settings['post_type_issslpg-template_image_panels'] )
		     || ! empty( $cr_settings['post_type_issslpg-template_keyword_panels'] )
		     || ! empty( $cr_settings['post_type_issslpg-template_phrase_panels'] )
		) {
			return;
		}

		// Set settings
		$cr_settings['post_type_issslpg-template_content_panels'] = "P1\nP2\nP3\nP4\nP5";
		$cr_settings['post_type_issslpg-template_image_panels'] = "General\nService";
		$cr_settings['post_type_issslpg-template_keyword_panels'] = "General\nService";
		$cr_settings['post_type_issslpg-template_phrase_panels'] = "PH1\nPH2\nPH3\nPH4\nPH5";

		update_option( 'issscr_settings', $cr_settings );

		// Add Example Service teamplate page with example data
		if ( ISSSLPG_Helpers::post_exists_by_slug( 'example-service', 'issslpg-template' ) ) {
			return;
		}

		$example_text_1 = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Phasellus hendrerit. Pellentesque aliquet nibh nec urna. In nisi neque, aliquet vel, dapibus id, mattis vel, nisi. Sed pretium, ligula sollicitudin laoreet viverra, tortor libero sodales leo, eget blandit nunc tortor eu nibh. Nullam mollis. Ut justo. Suspendisse potenti.';
		$example_text_2 = 'Sed egestas, ante et vulputate volutpat, eros pede semper est, vitae luctus metus libero eu augue. Morbi purus libero, faucibus adipiscing, commodo quis, gravida id, est. Sed lectus. Praesent elementum hendrerit tortor. Sed semper lorem at felis. Vestibulum volutpat, lacus a ultrices sagittis, mi neque euismod dui, eu pulvinar nunc sapien ornare nisl. Phasellus pede arcu, dapibus eu, fermentum et, dapibus sed, urna.';
		$example_text_3 = 'Morbi interdum mollis sapien. Sed ac risus. Phasellus lacinia, magna a ullamcorper laoreet, lectus arcu pulvinar risus, vitae facilisis libero dolor a purus. Sed vel lacus. Mauris nibh felis, adipiscing varius, adipiscing in, lacinia vel, tellus. Suspendisse ac urna. Etiam pellentesque mauris ut lectus. Nunc tellus ante, mattis eget, gravida vitae, ultricies ac, leo. Integer leo pede, ornare a, lacinia eu, vulputate vel, nisl.';
		$example_page_id = wp_insert_post( array(
			'post_type'    => 'issslpg-template',
			'post_title'   => 'Example Service',
			'post_name'    => 'example-service',
			'post_content' => ''
		) );
		add_post_meta( $example_page_id, '_issscr_randomizer_toggler', 'on' );
		add_post_meta( $example_page_id, '_issscr_issslpg-template_ph1_phrases', "Example PH1 Phrase 1\nExample PH1 Phrase 2\nExample PH1 Phrase 3" );
		add_post_meta( $example_page_id, '_issscr_issslpg-template_ph2_phrases', "Example PH2 Phrase 1\nExample PH2 Phrase 2\nExample PH2 Phrase 3" );
		add_post_meta( $example_page_id, '_issscr_issslpg-template_ph3_phrases', "Example PH3 Phrase 1\nExample PH3 Phrase 2\nExample PH3 Phrase 3" );
		add_post_meta( $example_page_id, '_issscr_issslpg-template_singular_general_keywords', "Singular General Keyword Example 1\nSingular General Keyword Example 2\nSingular General Keyword Example 3" );
		add_post_meta( $example_page_id, '_issscr_issslpg-template_plural_general_keywords', "Plural General Keyword Example 1\nPlural General Keyword Example 2\nPlural General Keyword Example 3" );
		add_post_meta( $example_page_id, '_issscr_issslpg-template_singular_service_keywords', "Singular Service Keyword Example 1\nSingular Service Keyword Example 2\nSingular Service Keyword Example 3" );
		add_post_meta( $example_page_id, '_issscr_issslpg-template_plural_service_keywords', "Plural Service Keyword Example 1\nPlural Service Keyword Example 2\nPlural Service Keyword Example 3" );
		add_post_meta( $example_page_id, '_issscr_issslpg-template_static_content', [
			['content' => "Static content example 1. {$example_text_1}"],
		] );
		add_post_meta( $example_page_id, '_issscr_issslpg-template_content', [
			['content' => "This is an example content block. You can add shortcodes to display a random content block from other content panels like this:\n[iss_p1_content]\n\nAnd like this:\n[iss_p2_content]\n\nYou can also display a random keyword from the Service Keyword panel: [iss_singular_service]\n\nOr a PH1 phrase: [iss_ph1_phrase]"],
		] );
		add_post_meta( $example_page_id, '_issscr_issslpg-template_p1_content', [
			['content' => "P1 Content Block 1 example text. {$example_text_1}"],
			['content' => "P1 Content Block 2 example text. {$example_text_2}"],
			['content' => "P1 Content Block 3 example text. {$example_text_3}"],
		] );
		add_post_meta( $example_page_id, '_issscr_issslpg-template_p2_content', [
			['content' => "P2 Content Block 1 example text. {$example_text_1}"],
			['content' => "P2 Content Block 2 example text. {$example_text_2}"],
			['content' => "P2 Content Block 3 example text. {$example_text_3}"],
		] );
		add_post_meta( $example_page_id, '_issscr_issslpg-template_p3_content', [
			['content' => "P3 Content Block 1 example text. {$example_text_1}"],
			['content' => "P3 Content Block 2 example text. {$example_text_2}"],
			['content' => "P3 Content Block 3 example text. {$example_text_3}"],
		] );
		add_post_meta( $example_page_id, '_issscr_issslpg-template_p4_content', [
			['content' => "P4 Content Block 1 example text. {$example_text_1}"],
			['content' => "P4 Content Block 2 example text. {$example_text_2}"],
			['content' => "P4 Content Block 3 example text. {$example_text_3}"],
		] );
		add_post_meta( $example_page_id, '_issscr_issslpg-template_page_meta_boxes', [
			['meta_description' => "Example Meta Description 1. {$example_text_1}"],
			['meta_description' => "Example Meta Description 2. {$example_text_2}"],
			['meta_description' => "Example Meta Description 3. {$example_text_3}"],
		] );
	}

}