<?php

namespace ssg_steam_news_core;

defined('ABSPATH') or die('No script kiddies please!');

function is_integer_in_range($value, $min_value, $max_value){

	$filter_options = array(
		'options' => array(
			'min_range' => $min_value,
			'max_range' => $max_value));

	return FALSE !== filter_var($value, FILTER_VALIDATE_INT, $filter_options);
}

?>
