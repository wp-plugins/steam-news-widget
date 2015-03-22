<?php

namespace ssg_steam_news_core;

defined('ABSPATH') or die('No script kiddies please!');

class wp_plugin_options_view{

	private $widget = NULL;
	private $option_set = NULL;
	private $control_array = array();

	public function __construct($widget, $option_set){

		$this->widget = $widget;
		$this->option_set = $option_set; 
	}

	public function add_control($id, $control_obj){

		$this->control_array[$id] = $control_obj;
	}

	public function render(){

		$result = '';
		$result .= $this->render_spacing();

		foreach($this->control_array as $id => $control_obj){

			$field_id = $this->widget->get_field_id($id);
			$field_name = $this->widget->get_field_name($id);
			$field_value = $this->option_set[$id];

			$result .= $control_obj->render($field_id, $field_name, $field_value);
		}
		return $result;
	}

	protected function render_spacing(){
		return '<p/>';
	}
}

?>
