<?php

namespace ssg_steam_news_core;

defined('ABSPATH') or die('No script kiddies please!');

class wp_url_fetcher{

	public function fetch($url){
		$response = wp_remote_get($url);
		if (is_wp_error($response)){
			$error_message = $response->get_error_message();
			throw new \Exception($error_message);
		}
		return wp_remote_retrieve_body($response);
	}
}

?>
