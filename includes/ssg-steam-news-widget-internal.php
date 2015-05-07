<?php

defined('ABSPATH') or die('No script kiddies please!');

require_once(__DIR__.'/swt-wp-cron.php');

class ssg_steam_news_widget extends WP_Widget{

	const CRON_HOOK_NAME = 'ssg-steam-news-cron-hook';
	const BASE_ID = 'ssg-steam-news';
	const STYLE = 'ssg-steam-news-style';

	const ARG_WIDGET_BEFORE_WIDGET = 'before_widget';
	const ARG_WIDGET_AFTER_WIDGET = 'after_widget';
	const ARG_WIDGET_BEFORE_TITLE = 'before_title';
	const ARG_WIDGET_AFTER_TITLE = 'after_title';

	const FILTER_WIDGET_TITLE = 'widget_title';

	const OPTION_TITLE = 'title';
	const DEFAULT_TITLE = 'Game News';

	const OPTION_STEAM_GAME_ID = 'steam-game-id';
	const DEFAULT_STEAM_GAME_ID = '440, 570, 730';

	const OPTION_MAX_NEWS_COUNT = 'max-news-count';
	const DEFAULT_MAX_NEWS_COUNT = 5;

	const OPTION_NEWS_LENGTH = 'news-length';
	const DEFAULT_NEWS_LENGTH = 256;

	const OPTION_UPDATE_TIME = 'update-time';

	const OPTION_HTML_TEMPLATE = 'html-template';
	const DEFAULT_HTML_TEMPLATE =
'<style>
.ssg-steam-news-widget-news {
	position: relative;
	margin-bottom: 5px;
}
.ssg-steam-news-widget-news-title{
	font-weight: bold;
}
.ssg-steam-news-widget-news-time {
	position: absolute;
	right: 5px;
	bottom: 5px;
	font: 10px Courier;
}
.ssg-steam-news-widget-news-text {
	display: block;
	font-size: 12px;
	font-weight: normal;
	padding: 10px 0px 24px 0px;
}
</style>
<div>
	<foreach iterator="{{news}}" in="{{newslist}}">
		<div class="ssg-steam-news-widget-news">
			<div class="ssg-steam-news-widget-news-title"><a href="{{news.url}}" target="_blank">{{news.title}}</a></div>
			<div class="ssg-steam-news-widget-news-text">{{news.text}}</div>
			<div class="ssg-steam-news-widget-news-time">{{news.date}}, {{news.time}}</div>
		</div>
	</foreach>
</div>';

	const OPTION_HTML_OUTPUT = 'html-output';
	const DEFAULT_HTML_OUTPUT = '';

	const OPTION_ERROR_MESSAGE = 'error-message';
	const DEFAULT_ERROR_MESSAGE = 'Options Saved';

	function __construct(){

		$widget_ops = array(
			'description' => 'Shows news for a selected game.'
		);

		parent::__construct(self::BASE_ID, 'Steam News', $widget_ops);
	}

	public function widget($args, $instance){

		if (isset($instance[self::OPTION_UPDATE_TIME])){
			echo '<!-- Steam News Widget was updated '.@date(DateTime::RFC1123, $instance[self::OPTION_UPDATE_TIME]).' -->';
		}

		if (isset($args[self::ARG_WIDGET_BEFORE_WIDGET])){
			echo $args[self::ARG_WIDGET_BEFORE_WIDGET];
		}

		if (isset($instance[self::OPTION_TITLE])){

			$title = $instance[self::OPTION_TITLE];
			if (!empty($title)){
				if (isset($args[self::ARG_WIDGET_BEFORE_TITLE])){
					echo $args[self::ARG_WIDGET_BEFORE_TITLE];
				}

				echo apply_filters(self::FILTER_WIDGET_TITLE, $title);

				if (isset($args[self::ARG_WIDGET_AFTER_TITLE])){
					echo $args[self::ARG_WIDGET_AFTER_TITLE];
				}
			}
		}

		if (isset($instance[self::OPTION_HTML_OUTPUT])){
			$result = $instance[self::OPTION_HTML_OUTPUT];
			echo $result;
		}

		if (isset($args[self::ARG_WIDGET_AFTER_WIDGET])){
			echo $args[self::ARG_WIDGET_AFTER_WIDGET];
		}
	}

	public function form($instance){

		require_once(__DIR__.'/swt-wp-gui-control-editbox.php');
		require_once(__DIR__.'/swt-wp-gui-control-errorbox.php');
		require_once(__DIR__.'/swt-wp-gui-control-textarea.php');
		require_once(__DIR__.'/swt-wp-plugin-options-view.php');

		$defaults = array(
			self::OPTION_TITLE => self::DEFAULT_TITLE,
			self::OPTION_STEAM_GAME_ID => self::DEFAULT_STEAM_GAME_ID,
			self::OPTION_MAX_NEWS_COUNT => self::DEFAULT_MAX_NEWS_COUNT,
			self::OPTION_NEWS_LENGTH => self::DEFAULT_NEWS_LENGTH,
			self::OPTION_HTML_TEMPLATE => self::DEFAULT_HTML_TEMPLATE,
		);

		$instance = wp_parse_args($instance, $defaults);

		$options_view = new \ssg_steam_news_core\wp_plugin_options_view($this, $instance);

		$options_view->add_control(self::OPTION_TITLE,
			new \ssg_steam_news_core\wp_gui_control_editbox('Title:'));
		$options_view->add_control(self::OPTION_STEAM_GAME_ID,
			new \ssg_steam_news_core\wp_gui_control_editbox('Steam Game IDs(","-separated list):'));
		$options_view->add_control(self::OPTION_MAX_NEWS_COUNT,
			new \ssg_steam_news_core\wp_gui_control_editbox('Maximum Number of News:'));
		$options_view->add_control(self::OPTION_NEWS_LENGTH,
			new \ssg_steam_news_core\wp_gui_control_editbox('News Length:'));
		$options_view->add_control(self::OPTION_HTML_TEMPLATE,
			new \ssg_steam_news_core\wp_gui_control_textarea('HTML Template:', 16, 16));

		if (array_key_exists(self::OPTION_ERROR_MESSAGE, $instance)){

			$error_occurred =
				!(self::DEFAULT_ERROR_MESSAGE === $instance[self::OPTION_ERROR_MESSAGE]); 
	
			$options_view->add_control(self::OPTION_ERROR_MESSAGE,
				new \ssg_steam_news_core\wp_gui_control_errorbox($error_occurred));
		}

		echo $options_view->render();
	}

	public function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance[self::OPTION_TITLE] = strip_tags($new_instance[self::OPTION_TITLE]);
		$instance[self::OPTION_STEAM_GAME_ID] = strip_tags($new_instance[self::OPTION_STEAM_GAME_ID]);
		$instance[self::OPTION_MAX_NEWS_COUNT] = strip_tags($new_instance[self::OPTION_MAX_NEWS_COUNT]);
		$instance[self::OPTION_NEWS_LENGTH] = strip_tags($new_instance[self::OPTION_NEWS_LENGTH]);
		$instance[self::OPTION_HTML_TEMPLATE] = $new_instance[self::OPTION_HTML_TEMPLATE];

		return $this->fetch_data($instance);
	}

	protected function get_news_generator(){
		require_once(__DIR__.'/swt-wp-steam-news-generator.php');
		return new \ssg_steam_news_core\wp_steam_news_generator;
	}

	protected function fetch_data($instance){

		$game_id = $instance[self::OPTION_STEAM_GAME_ID];
		$news_count = $instance[self::OPTION_MAX_NEWS_COUNT];
		$news_length = $instance[self::OPTION_NEWS_LENGTH];
		$template_in = $instance[self::OPTION_HTML_TEMPLATE];

		$news_gen = $this->get_news_generator();

		try{
			$instance[self::OPTION_HTML_OUTPUT] =
				$news_gen->render($template_in, $game_id, $news_count, $news_length);
			$instance[self::OPTION_ERROR_MESSAGE] = self::DEFAULT_ERROR_MESSAGE;
			$instance[self::OPTION_UPDATE_TIME] = time();
		}
		catch(Exception $e){
			$instance[self::OPTION_ERROR_MESSAGE] = $e->getMessage();
		}
		return $instance;
	}

	private static function is_instance_valid($number, $instance){
		return(is_numeric($number)
			and array_key_exists(self::OPTION_TITLE, $instance)
			and array_key_exists(self::OPTION_STEAM_GAME_ID, $instance)
			and array_key_exists(self::OPTION_MAX_NEWS_COUNT, $instance)
			and array_key_exists(self::OPTION_NEWS_LENGTH, $instance)
			and array_key_exists(self::OPTION_HTML_TEMPLATE, $instance));
	}

	public function fetch_data_ex(){

		$all_instances = $this->get_settings();
		$new_instances = array();

		foreach($all_instances as $number => $instance){

			if (self::is_instance_valid($number, $instance)){
				$new_instance = $this->fetch_data($instance);
				$new_instances[$number] = $new_instance;	
			}
		}
		$this->save_settings($new_instances);
	}

	public function deactivate(){
		\ssg_steam_news_core\cron_clear_callbacks(self::CRON_HOOK_NAME);
	}

	public static function uninstall(){

		$widget = new ssg_steam_news_widget;

		if (isset($widget->option_name)){
			delete_option($widget->option_name);
		}

		if (isset($widget->alt_option_name)){
			delete_option($widget->alt_option_name);
		}
	}
}

add_action('widgets_init',
	create_function('', 'return register_widget("ssg_steam_news_widget");'));

\ssg_steam_news_core\cron_register_callback(ssg_steam_news_widget::CRON_HOOK_NAME);

add_action(ssg_steam_news_widget::CRON_HOOK_NAME,
	array(new ssg_steam_news_widget, 'fetch_data_ex'));

?>
