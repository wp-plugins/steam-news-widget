<?php

namespace ssg_steam_news_core;

defined('ABSPATH') or die('No script kiddies please!');

require_once(__DIR__.'/swt-wp-gui-control.php');
require_once(__DIR__.'/swt-html-generator.php');

class wp_gui_control_errorbox extends wp_gui_control{

	const STYLE_RED =
		'background: #ffeeee; color: red; border: 2px solid #ee0000; font-style: italic; font-weight: 900; text-align: center;';
	const STYLE_GREEN =
		'background: #eeffee; color: green; border: 2px solid #00ee00; font-style: italic; font-weight: 900; text-align: center;';

	private $error;

	public function __construct($error){
		parent::__construct('');
		$this->error = $error;
	}

	protected function render_internal($field_id, $field_name, $field_value){

		$control = new tag_paragraph_styled($this->error? self::STYLE_RED:self::STYLE_GREEN, $field_value);
		return $control;
	}
}

?>
