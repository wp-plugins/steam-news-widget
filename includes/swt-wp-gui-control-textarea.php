<?php

namespace ssg_steam_news_core;

defined('ABSPATH') or die('No script kiddies please!');

require_once(__DIR__.'/swt-wp-gui-control.php');
require_once(__DIR__.'/swt-html-generator.php');

class wp_gui_control_textarea extends wp_gui_control{

	private $cols = 10;
	private $rows = 10;

	public function __construct($label, $cols, $rows){
		parent::__construct($label);
		$this->cols = $cols;
		$this->rows = $rows;
	}

	protected function render_internal($field_id, $field_name, $field_value){

		$control = new tag_textarea(
			self::CSS_CLASS, $field_id, $field_name, $field_value, $this->cols, $this->rows);
		return $control;
	}
}

?>
