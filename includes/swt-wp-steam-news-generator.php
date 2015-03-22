<?php

namespace ssg_steam_news_core;

defined('ABSPATH') or die('No script kiddies please!');

require_once(__DIR__.'/swt-steam-news-generator-base.php');

class wp_steam_news_generator extends steam_news_generator_base{

	protected function get_news_fetcher(){
		require_once(__DIR__.'/swt-wp-steam-news-fetcher.php');
		return new wp_steam_news_fetcher;
	}

	protected function get_timezone(){
		require_once(__DIR__.'/swt-wp-time.php');
		return wp_get_timezone();
	}

	protected function get_date_format(){
		require_once(__DIR__.'/swt-wp-time.php');
		return wp_get_date_format();
	}

	protected function get_time_format(){
		require_once(__DIR__.'/swt-wp-time.php');
		return wp_get_time_format();
	}
}

?>
