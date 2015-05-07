<?php

namespace ssg_steam_news_core;

defined('ABSPATH') or die('No script kiddies please!');

class wp_cron_manager{

	private $hook_name = NULL;

	public function __construct($hook_name){
		$this->hook_name = $hook_name;
	}

	public function clear_callbacks(){
		wp_clear_scheduled_hook($this->hook_name);
	}

	public function register_callback(){

		if (!wp_next_scheduled($this->hook_name)){
			wp_schedule_event(time(), 'hourly', $this->hook_name);
		}
	}
}

function cron_register_callback($hook_name){
	$cron_manager = new wp_cron_manager($hook_name);
	$cron_manager->register_callback();
}

function cron_clear_callbacks($hook_name){
	$cron_manager = new wp_cron_manager($hook_name);
	$cron_manager->clear_callbacks();
}

?>
