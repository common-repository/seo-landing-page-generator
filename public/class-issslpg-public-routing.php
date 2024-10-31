<?php

class ISSSLPG_Public_Routing {

	private $page_name_format = '(.?.+?)';
	private $location_format  = '([^&]+)';
	private $id_format        = '([0-9]+)';

	public function register_rewrite_tags() {
		add_rewrite_tag( '%xml_sitemap%',      $this->page_name_format );
		add_rewrite_tag( '%city%',             $this->location_format );
		add_rewrite_tag( '%city_id%',          $this->location_format );
		add_rewrite_tag( '%county%',           $this->location_format );
		add_rewrite_tag( '%county_id%',        $this->location_format );
		add_rewrite_tag( '%state%',            $this->location_format );
		add_rewrite_tag( '%state_id%',         $this->location_format );
		add_rewrite_tag( '%country%',          $this->location_format );
		add_rewrite_tag( '%country_id%',       $this->location_format );
		add_rewrite_tag( '%show_pages%',       $this->location_format );
		add_rewrite_tag( '%template_page_id%', $this->location_format );
	}

	public function register_xml_sitemap_rewrite_rules() {
//		add_rewrite_rule(
////			'sitemap(-+([a-zA-Z0-9_-]+))?\.xml$',
//			'sitemap_index.xml',
//			plugins_url( "/", dirname( __FILE__ ) ),
//			'top'
//		);
//		var_dump(plugins_url( "sitemaps/sitemap_index.xml", dirname( __FILE__ ) ));
	}

	public function register_sitemap_rewrite_rules() {

		$slug = ISSSLPG_Options::get_html_sitemap_setting( 'html_sitemap_slug', 'sitemap' );

		// Sitemap Cities Page: County ID / Landing Page ID
		add_rewrite_rule(
			"^{$slug}/county/{$this->id_format}/tp/{$this->id_format}/?$",
			'index.php?pagename='. $slug .'&county_id=$matches[1]&template_page_id=$matches[2]',
			'top'
		);

		// Sitemap Counties Page: State ID / Landing Page ID
		add_rewrite_rule(
			"^{$slug}/state/{$this->id_format}/tp/{$this->id_format}/?$",
			'index.php?pagename='. $slug .'&state_id=$matches[1]&template_page_id=$matches[2]',
			'top'
		);

		// Sitemap Pages Page: State ID
		add_rewrite_rule(
			"^{$slug}/state/{$this->id_format}/?$",
			'index.php?pagename='. $slug .'&state_id=$matches[1]',
			'top'
		);

		// Sitemap Pages Page: Country ID
		add_rewrite_rule(
			"^{$slug}/country/{$this->id_format}/?$",
			'index.php?pagename='. $slug .'&country_id=$matches[1]',
			'top'
		);

		// Sitemap Cities Page: State / County
//		add_rewrite_rule(
//			"^{$slug}/{$this->location_format}/{$this->location_format}/?$",
//			'index.php?pagename='. $slug .'&state=$matches[1]&county=$matches[2]',
//			'top'
//		);

		// Sitemap Counties Page: State
//		add_rewrite_rule(
//			"^{$slug}/{$this->location_format}/?$",
//			'index.php?pagename='. $slug .'&state=$matches[1]',
//			'top'
//		);
	}

	public function register_location_rewrite_rules() {

		// City Page: City / State / ID
		add_rewrite_rule(
			"^{$this->page_name_format}/{$this->location_format}/{$this->location_format}/{$this->id_format}/?$",
			'index.php?pagename=$matches[1]&city=$matches[2]&state=$matches[3]&city_id=$matches[4]',
			'top'
		);

		// City Page: City / State
		add_rewrite_rule(
			"^{$this->page_name_format}/{$this->location_format}/{$this->location_format}/?$",
			'index.php?pagename=$matches[1]&city=$matches[2]&state=$matches[3]',
			'top'
		);

		// County Page: County / State
		add_rewrite_rule(
			"^county/{$this->id_format}/{$this->page_name_format}/{$this->location_format}/{$this->location_format}/?$",
			'index.php?county_id=$matches[1]&pagename=$matches[2]&county=$matches[3]&state=$matches[4]',
			'top'
		);

	}

	public function flush_rewrite_rules() {
		if ( get_option( 'issslpg_flush_rewrite_rules_flag' ) ) {
			flush_rewrite_rules();
			delete_option( 'issslpg_flush_rewrite_rules_flag' );
		}
	}

}