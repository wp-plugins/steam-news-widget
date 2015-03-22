<?php

namespace ssg_steam_news_core;

defined('ABSPATH') or die('No script kiddies please!');

function str_stpip_urls($text){
	return preg_replace('%(?<!(ref=("|\')|(href=)))(\b[\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])%', '', $text);
}

function str_stpip_em_tags($text){
	return preg_replace('/<(\s)*(\/)?(\s)*em((\s)+[\s\S]*)?>/i', '', $text);
}

?>