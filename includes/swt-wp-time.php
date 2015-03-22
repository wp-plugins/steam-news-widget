<?php

namespace ssg_steam_news_core;

defined('ABSPATH') or die('No script kiddies please!');

function wp_get_timezone(){
	$timezone = get_option('timezone_string');
	if (!empty($timezone)){
		$current_timezone = @date_default_timezone_get();
		$valid = @date_default_timezone_set($timezone);
		if ($current_timezone){
			date_default_timezone_set($current_timezone);
		}
		if ($valid){
			return $timezone;
		}
	}
	return 'UTC';
}

function wp_get_date_format(){
	$date_format = get_option('date_format');
	if (!empty($date_format)){
		return $date_format;
	}
	return 'F j, Y';
}

function wp_get_time_format(){
	$time_format = get_option('time_format');
	if (!empty($time_format)){
		return $time_format;
	}
	return 'F j, Y';
}

?>
