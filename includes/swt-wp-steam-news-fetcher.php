<?php

namespace ssg_steam_news_core;

defined('ABSPATH') or die('No script kiddies please!');

require_once(__DIR__.'/swt-steam-news-fetcher-base.php');

class wp_steam_news_fetcher extends steam_news_fetcher_base{

	protected function get_data_fetcher(){
		require_once(__DIR__.'/swt-wp-url-fetcher.php');
		return new wp_url_fetcher;
	}
}

?>
