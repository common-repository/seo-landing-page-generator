<?php

class ISSSLPG_Remote_Data {

	public function load_table_part( $unit_category, $unit_id, $table_name, $offset = 0 ) {
		$api_response = $this->get_api_response( array(
			$unit_category,
			$table_name,
			'unit',
			(int)$unit_id,
			$offset,
		) );
		if ( ! isset( $api_response->data ) ) {
			return null;
		}

		return $api_response->data;
	}

	public function load_total_item_count( $unit_category, $unit_id, $table_name ) {
		$api_response = $this->get_api_response( array(
			$unit_category,
			$table_name,
			'unit',
			(int)$unit_id,
			'count',
		) );
		if ( ! isset( $api_response->data->total_item_count ) ) {
			return null;
		}

		return $api_response->data->total_item_count;
	}

	public function load_api_version() {
		$api_response = $this->get_api_response( array(
			'version',
		) );
		if ( ! isset( $api_response->data->api_version ) ) {
			return null;
		}

		return $api_response->data->api_version;
	}

	protected function get_parameter_url_part( $api_url_parameters ) {
		if ( ! is_array( $api_url_parameters ) || empty( $api_url_parameters ) ) {
			return false;
		}

		return join( '/', $api_url_parameters );
	}

	protected function get_api_response( $api_url_parameters ) {
		// Source: https://support.liveagent.com/061754-How-to-make-REST-calls-in-PHP
		$api_parameter_url_part = $this->get_parameter_url_part( $api_url_parameters );
//		$api_base_url = 'http://iss-api-v1.us-east-1.elasticbeanstalk.com/api/';
		$api_base_url = 'https://issapi.amsserv1.com/public/api/';
		$curl = curl_init();
		curl_setopt_array( $curl, array(
			CURLOPT_URL => $api_base_url . $api_parameter_url_part,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache"
		  	),
		) );

		$response = curl_exec( $curl );
		$err = curl_error( $curl );

		curl_close( $curl );
		$response = json_decode( $response ); //because of true, it's in an array

		return $response;
	}

}