<?php

namespace ssg_steam_news_core;

defined('ABSPATH') or die('No script kiddies please!');

class steam_news{

	private $title = NULL;
	private $url = NULL;
	private $author = NULL;
	private $feed_label = NULL;
	private $timestamp =  NULL;
	private $text = NULL;

	public function __construct(
		$title, $url, $author, $feed_label, $date, $text){

		$this->title = $title;
		$this->url = $url;
		$this->author = $author;
		$this->feed_label = $feed_label;
		$this->timestamp = $date;
		$this->text = $text;
	}

	public function get_title(){
		return $this->title;
	}

	public function get_url(){
		return $this->url;
	}

	public function get_author(){
		return $this->author;
	}

	public function get_feed_label(){
		return $this->feed_label;
	}

	public function get_timestamp(){
		return $this->timestamp;
	}

	public function get_text(){
		return $this->text;
	}
}

abstract class steam_news_fetcher_base{

	const MEMBER_APPNEWS = 'appnews';
	const MEMBER_NEWS_ITEMS = 'newsitems';
	const MEMBER_TITLE = 'title';
	const MEMBER_URL = 'url';
	const MEMBER_AUTHOR = 'author';
	const MEMBER_FEED_LABEL = 'feedlabel';
	const MEMBER_DATE = 'date';
	const MEMBER_CONTENTS = 'contents';

	const MAX_ALLOWED_APP_ID = 1000000;
	const MAX_ALLOWED_NUMBER_OF_NEWS = 64;
	const MAX_ALLOWED_NEWS_LENGTH = 1024;

	abstract protected function get_data_fetcher();

	public function fetch($app_id, $news_count, $max_news_length){

		require_once(__DIR__.'/swt-numbers.php');
		require_once(__DIR__.'/swt-string-ops.php');

		if (!is_integer_in_range($app_id, 1, self::MAX_ALLOWED_APP_ID)){
			$msg = sprintf(
				'The steam app id must  be a positive integer number in range [1, %d]. "%s" was provided.',
				self::MAX_ALLOWED_APP_ID,
				$app_id);
			throw new \Exception($msg);
		}

		if (!is_integer_in_range($news_count, 1, self::MAX_ALLOWED_NUMBER_OF_NEWS)){
			$msg = sprintf(
				'The number of news must be a positive integer number in range [1, %d]. "%s" was provided.',
				self::MAX_ALLOWED_NUMBER_OF_NEWS,
				$news_count);
			throw new \Exception($msg);
		}

		if (!is_integer_in_range($max_news_length, 1, self::MAX_ALLOWED_NEWS_LENGTH)){
			$msg = sprintf(
				'The news length must be a positive integer number in range [1, %d]. "%s" was provided.',
				self::MAX_ALLOWED_NEWS_LENGTH,
				$max_news_length);
			throw new \Exception($msg);
		}

		$query_base = 'http://api.steampowered.com/ISteamNews/GetNewsForApp/v0002/?';

		$news_list = array();

		$query_params = array(
			'appid'     => $app_id,
			'count'     => $news_count,
			'maxlength' => $max_news_length,
			'format'    => 'json',
		);

		$query = $query_base.http_build_query($query_params);

		$data_fetcher = $this->get_data_fetcher();
		$data = $data_fetcher->fetch($query);

		$decoded_info = json_decode($data, true);
		$news_set = $decoded_info[self::MEMBER_APPNEWS][self::MEMBER_NEWS_ITEMS];

		foreach($news_set as $news){

			$news_title = $news[self::MEMBER_TITLE];
			$news_url = $news[self::MEMBER_URL];
			$news_author = $news[self::MEMBER_AUTHOR];
			$news_feedlabel = $news[self::MEMBER_FEED_LABEL];
			$news_date = $news[self::MEMBER_DATE];
			$news_text = str_stpip_em_tags(str_stpip_urls($news[self::MEMBER_CONTENTS]));

			$steam_news = new steam_news(
				$news_title, $news_url, $news_author, $news_feedlabel, $news_date, $news_text);
			array_push($news_list, $steam_news);
		}
		return $news_list;
	} 
}

?>