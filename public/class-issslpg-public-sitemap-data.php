<?php

use WeDevs\ORM\WP\Country as Country;
use WeDevs\ORM\WP\State as State;
use WeDevs\ORM\WP\County as County;

class ISSSLPG_Public_Sitemap_Data {

	private $page_url;

	public function __construct() {
		$this->page_url = $this->get_page_url();
	}

	public function get_page_url() {
		$url = get_the_permalink();
		$url_length = strlen( $url );

		if ( $url[$url_length-1] != '/' ) {
			$url.= '/';
		}

		return $url;
	}

	public function get_country() {
		$countries = Country::all();
		$output = array();
		foreach ( $countries as $country ) {
			if ( $country->countryData && $country->countryData->active ) {
				$output[ $country->id ] = $country->name;
			}
		}

		return $output;
	}

	public function get_states() {
		global $wp_query;

		$country = false;
		if ( isset( $wp_query->query_vars['country_id'] ) ) {
			$country = Country::where( 'id', $wp_query->query_vars['country_id'] )->first();
		}

		if ( $country ) {
			$states = $country->states;
			if ( $states ) {
				$output = array();
				foreach ( $states as $state ) {
					if ( $state->stateData && $state->stateData->active ) {
						$output[ $state->id ] = $state->name;
					}
				}
				// TODO: This is a temporary solution.
				$output = ISSSLPG_Helpers::reduce_array_by_county_limit( $output );
				return $output;
			}
		}

		return false;
	}

	public function get_pages() {
		$pages = get_posts( array(
			'post_type'      => 'issslpg-template',
			'posts_per_page' => -1,
		) );
		$pages_array = array();
		foreach( $pages as $page ) {
			$pages_array[ $page->ID ] = $page->post_title;
		}

		return $pages_array;
	}

	public function get_counties() {
		global $wp_query;

		$state = false;
		if ( isset( $wp_query->query_vars['state_id'] ) ) {
			$state = State::where( 'id', $wp_query->query_vars['state_id'] )->first();
		}
//		elseif ( isset( $wp_query->query_vars['state'] ) ) {
//			$state_query_name = ISSSLPG_Url_Helpers::title_to_query( $wp_query->query_vars['state'] );
//			$state = State::where( 'name', $state_query_name )->first();
//		}

		if ( $state ) {
			$counties = $state->counties;
			if ( $counties ) {
				$output = array();
				foreach ( $counties as $county ) {
					if ( $county->countyData && $county->countyData->active ) {
						$output[ $county->id ] = $county->name;
					}
				}
				// TODO: This is a temporary solution.
				$output = ISSSLPG_Helpers::reduce_array_by_county_limit( $output );
				return $output;
			}
		}

		return false;
	}

	public function get_cities() {
		global $wp_query;

		$county = false;
		if ( isset( $wp_query->query_vars['county_id'] ) ) {
			$county = County::where( 'id', $wp_query->query_vars['county_id'] )->first();
		}
//		elseif ( isset( $wp_query->query_vars['state'] ) && isset( $wp_query->query_vars['county'] ) ) {
//			$state_query_name  = ISSSLPG_Url_Helpers::title_to_query( $wp_query->query_vars['state'] );
//			$county_query_name = ISSSLPG_Url_Helpers::title_to_query( $wp_query->query_vars['county'] );
//
//			$state  = State::where( 'name', $state_query_name )->first();
//			$county = County::where( 'name', $county_query_name )->where( 'state_id', $state->id )->first();
//		}

		if ( $county ) {
			$cities = $county->cities;
			if ( $cities ) {
				$output = array();
				foreach ( $cities as $city ) {
					if ( $city->cityData && $city->cityData->active ) {
						$output[ $city->id ] = $city->name;
					}
				}
				shuffle($output);
				return $output;
			}
		}

		return false;
	}

	public function get_country_list() {
		$country = $this->get_country();

		if ( is_array( $country ) && ! empty( $country ) ) {
			$output = '';
			$output.= '<ul>';
			foreach ( $country as $country_id => $country_name ) {
				$url = "{$this->page_url}country/{$country_id}/";
				$output.= "<li><a href='{$url}'>{$country_name}</a></li>";
			}
			$output.= '</ul>';

			return $output;
		}

		return false;
	}

	public function get_states_list() {
		$states = $this->get_states();

		if ( is_array( $states ) && ! empty( $states ) ) {
			$output = '';
			$output.= '<ul>';
			foreach ( $states as $state_id => $state_name ) {
				$url = "{$this->page_url}state/{$state_id}/";
				$output.= "<li><a href='{$url}'>{$state_name}</a></li>";
			}
			$output.= '</ul>';
			return $output;
		}

		return false;
	}

	public function get_xml_sitemap_list() {
		$is_xml_sitemap_active = ISSSLPG_Options::get_xml_sitemap_setting( 'activate_xml_sitemaps', true );
		$show_xml_sitemap_link = ISSSLPG_Options::get_html_sitemap_setting( 'html_sitemap_show_xml_sitemap_link', true );
		if ( ! $is_xml_sitemap_active || ! $show_xml_sitemap_link ) {
			return;
		}

		$xml_sitemaps = new ISSSLPG_Public_XML_Sitemap_Generator();
		$xml_sitemap_url = $xml_sitemaps->sitemap_url . '/sitemap_index.xml';

		$output = '<ul>';
		$output.=     "<li><a href='{$xml_sitemap_url}'>XML Sitemap</a></li>";
		$output.= '</ul>';

		return $output;
	}

	public function get_pages_list() {
		global $wp_query;
		$pages = $this->get_pages();

		if ( is_array( $pages ) && ! empty( $pages ) && isset( $wp_query->query_vars['state_id'] ) ) {
			$state_id = $wp_query->query_vars['state_id'];
			$output = [];
			foreach ( $pages as $page_id => $page_name ) {
				$show_page = ISSSLPG_Options::get_html_sitemap_setting( "html_sitemap_include_template_page_{$page_id}", true );
				if ( $show_page ) {
					$url = "{$this->page_url}state/{$state_id}/tp/{$page_id}/";
					$output[]= "<li><a href='{$url}'>{$page_name}</a></li>";
				}
			}
			shuffle( $output );
			return '<ul>' . join( '', $output ) . '</ul>';
		}

		return false;
	}

	public function get_counties_list() {
		global $wp_query;
		$counties = $this->get_counties();

		if ( is_array( $counties ) && ! empty( $counties ) && isset( $wp_query->query_vars['template_page_id'] ) ) {
			$page_id = $wp_query->query_vars['template_page_id'];
			$page_name = get_the_title( $page_id );
			$output = '';
			$output.= '<ul>';
			foreach ( $counties as $county_id => $county_name ) {
				$url = "{$this->page_url}county/{$county_id}/tp/{$page_id}/";
				$output.= "<li><a href='{$url}'>{$page_name} in {$county_name}</a></li>";
			}
			$output.= '</ul>';
			return $output;
		}

		return false;
	}

	public function get_landing_pages() {
		global $wp_query;
		$pages = array();

		if ( isset( $wp_query->query_vars['county_id'] ) && isset( $wp_query->query_vars['template_page_id'] ) ) {

			$template_page_id = $wp_query->query_vars['template_page_id'];
			$county_id        = $wp_query->query_vars['county_id'];

			$landing_pages = new WP_Query( array(
				'post_type'      => 'issslpg-landing-page',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'meta_query'  => array(
					'relation' => 'AND',
					array(
						'key'     => '_issslpg_template_page_id',
						'value'   => (string) $template_page_id,
						'compare' => '='
					),
					array(
						'key'     => '_issslpg_county_id',
						'value'   => (string) $county_id,
						'compare' => '='
					),
				),
			) );

			while ( $landing_pages->have_posts() ) :
				$landing_pages->the_post();
				$page_id = get_the_ID();
				$pages[$page_id]['url']   = get_the_permalink();
//				$pages[$page_id]['title'] = get_the_title();
				$pages[$page_id]['title'] = get_post_meta( get_the_ID(), '_issslpg_page_title', true );
			endwhile;
			wp_reset_postdata();
		}

		shuffle($pages);
		return $pages;
	}

	public function get_landing_pages_list() {
		global $wp_query;

		if ( isset( $wp_query->query_vars['county_id'] ) && isset( $wp_query->query_vars['template_page_id'] ) ) {
			$landing_pages = $this->get_landing_pages();

			if ( is_array( $landing_pages ) && ! empty( $landing_pages ) ) {
				$output = '';
				$output .= '<ul>';
				foreach ( $landing_pages as $landing_page ) {
					$url   = $landing_page['url'];
					$title = $landing_page['title'];
					$output .= "<li><a href='{$url}'>{$title}</a></li>";
				}
				$output .= '</ul>';

				return $output;
			}
		}

		return false;
	}

	public function get_cities_list() {
		global $wp_query;
		$cities = $this->get_cities();

		if ( is_array( $cities ) && ! empty( $cities ) ) {

			// To determine a city URL, we need to know the state name
			$state_slug = '';
			if ( isset( $wp_query->query_vars['county_id'] ) ) {
				$county     = County::where( 'id', $wp_query->query_vars['county_id'] )->first();
				$state_slug = ISSSLPG_Url_Helpers::title_to_slug( $county->state->name );
			}

			if ( ! empty( $state_slug ) && isset( $wp_query->query_vars['template_page_id'] ) ) {
				$page = get_post( $wp_query->query_vars['template_page_id'] );
				$page_slug = $page->post_name;
				$page_name = $page->post_title;
				$output = '';
				$output .= '<ul>';
				foreach ( $cities as $city_id => $city_name ) {
					$city_slug = ISSSLPG_Url_Helpers::title_to_slug( $city_name );
					$url = "{$this->page_url}{$page_slug}/{$city_slug}/{$state_slug}/{$city_id}/";
					$output .= "<li><a href='{$url}'>{$page_name} in {$city_name}</a></li>";
				}
				$output .= '</ul>';

				return $output;
			}
		}

		return false;
	}

}