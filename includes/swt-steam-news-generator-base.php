<?php

namespace ssg_steam_news_core;

defined('ABSPATH') or die('No script kiddies please!');

abstract class steam_news_generator_base{

	const TEMPLATE_PARAM_NEWS_LIST = 'newslist';
	const TEMPLATE_PARAM_MEMBER_TITLE = 'title';
	const TEMPLATE_PARAM_MEMBER_TEXT = 'text';
	const TEMPLATE_PARAM_MEMBER_DATE = 'date';
	const TEMPLATE_PARAM_MEMBER_TIME = 'time';
	const TEMPLATE_PARAM_MEMBER_URL = 'url';
	const TEMPLATE_PARAM_MEMBER_AUTHOR = 'author';
	const TEMPLATE_PARAM_MEMBER_FEED_LABEL = 'feedlabel';
	const TEMPLATE_PARAM_UPDATE_TIME = 'updatetimestamp';

	abstract protected function get_news_fetcher();
	abstract protected function get_timezone();
	abstract protected function get_date_format();
	abstract protected function get_time_format();

	protected function get_template_processor(){
		require_once(__DIR__.'/swt-template-processor.php');
		return new template_processor;
	}

	public function render($html_template, $app_id, $news_count, $news_length){

		$news_fetcher = $this->get_news_fetcher();
		$news_list  = $news_fetcher->fetch($app_id, $news_count, $news_length);

		if (empty($news_list)){
			throw new \Exception(sprintf('No news found for app id "%d".', $app_id));
		}

		$template_params = array(
			self::TEMPLATE_PARAM_UPDATE_TIME => time(),
			self::TEMPLATE_PARAM_NEWS_LIST => array()
		);

		$news_list = array_slice($news_list, 0, (int)$news_count);

		foreach($news_list as $news){

			$news_title = $news->get_title();
			$news_text = $news->get_text();
			$news_timestamp = $news->get_timestamp();
			$news_url = $news->get_url();
			$news_author = $news->get_author();
			$news_feed_label = $news->get_feed_label();

			@date_default_timezone_set($this->get_timezone());
			$news_date = @date($this->get_date_format(), (int)$news_timestamp);
			$news_time = @date($this->get_time_format(), (int)$news_timestamp);

			$news = array(
				self::TEMPLATE_PARAM_MEMBER_TITLE => $news_title,
				self::TEMPLATE_PARAM_MEMBER_TEXT => $news_text,
				self::TEMPLATE_PARAM_MEMBER_DATE => $news_date,
				self::TEMPLATE_PARAM_MEMBER_TIME => $news_time,
				self::TEMPLATE_PARAM_MEMBER_URL => $news_url,
				self::TEMPLATE_PARAM_MEMBER_AUTHOR => $news_author,
				self::TEMPLATE_PARAM_MEMBER_FEED_LABEL => $news_feed_label,
			);

			array_push($template_params[self::TEMPLATE_PARAM_NEWS_LIST], $news);
		}

		$template_processor = $this->get_template_processor();
		$result = $template_processor->run($html_template, $template_params);
		return html_entity_decode($result);
	}
}

?>
