<?php

namespace ssg_steam_news_core;

defined('ABSPATH') or die('No script kiddies please!');

require_once(__DIR__.'/swt-wp-gui-control.php');
require_once(__DIR__.'/swt-html-generator.php');

class wp_gui_control_editbox extends wp_gui_control{

	public function __construct($label){
		parent::__construct($label);
	}

	protected function render_internal($field_id, $field_name, $field_value){

		$control = html_input_text(self::CSS_CLASS, $field_id, $field_name, $field_value);
		return $control;
	}
}

?>
