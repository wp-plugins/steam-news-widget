<?php
/*
Plugin Name: Steam News Widget
Plugin URI: http://www.pcgametricks.com/
Description: Shows news for selected games.
Version: 1.1.1
Author: Softsultant
*/

defined('ABSPATH') or die('No script kiddies please!');

version_compare(phpversion(), '5.3.3', '>=')
	or die('The software requires PHP 5.3.3 or higher. The actual version is '.phpversion().".\n");

require_once(__DIR__.'/includes/ssg-steam-news-widget-internal.php');

$ssg_steam_news_widget_inst = new ssg_steam_news_widget;
register_deactivation_hook(__FILE__, array($ssg_steam_news_widget_inst, 'deactivate'));
register_uninstall_hook(__FILE__, array('ssg_steam_news_widget', 'uninstall'));

?>
