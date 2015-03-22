<?php

namespace ssg_steam_news_core;

defined('ABSPATH') or die('No script kiddies please!');

require_once(__DIR__.'/swt-html-generator.php');

abstract class wp_gui_control{

	const CSS_CLASS = 'widefat';

	private $label = NULL;

	public function __construct($label){
		$this->label = $label;
	}

	/* this function must be emplemented by child class */
	abstract protected function render_internal($field_id, $field_name, $field_value);

	final public function render($field_id, $field_name, $field_value){

		$paragraph = html_paragraph();

		if (!empty($this->label)){
			$paragraph->add(html_label($field_id, $this->label));
		}

		$paragraph->add($this->render_internal($field_id, $field_name, $field_value));

		return $paragraph->render();
	}
}

?>
