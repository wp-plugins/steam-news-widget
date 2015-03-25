<?php

namespace ssg_steam_news_core;

defined('ABSPATH') or die('No script kiddies please!');

require_once(__DIR__.'/swt-url-fetcher.php');

class wp_url_fetcher extends url_fetcher{

	public function fetch($url_array){

		if (count($url_array) > 1 and $this->is_curl_supported()){
			return parent::fetch($url_array);
		}

		$results = array();
		foreach($url_array as $url){
			$response = wp_remote_get($url);
			if (is_wp_error($response)){
				$error_message = $response->get_error_message();
				throw new \Exception($error_message);
			}
			array_push($results, wp_remote_retrieve_body($response));
		}
		return $results;
	}
}

?>
