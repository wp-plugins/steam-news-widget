<?php

namespace ssg_steam_news_core;

defined('ABSPATH') or die('No script kiddies please!');

class tag{

	private $tag_name = '';
	private $text = NULL;
	private $params = array();
	private $children = array();

	public function  __construct($tag_name){
		$this->tag_name = $tag_name;
	}

	final public function render($indent=''){
		$xmlstring = $indent.'<'.$this->tag_name;
		if (!empty($this->params)){
			ksort($this->params);
			foreach($this->params as $key => $value){
				$xmlstring .= ' '.$key.'="'.$value.'"';	
			}	
		}
		$xmlstring .= '>';

		if (!empty($this->children)){
			$xmlstring .= "\n";
			foreach($this->children as $child){
				$xmlstring .= $child->render($indent.' ');
			}
			$xmlstring .= "\n".$indent;
		}
		else if (!empty($this->text)){
			$xmlstring .= $this->text;
		}
		$xmlstring .= '</'.$this->tag_name.'>';
		return $xmlstring;
	}

	final protected function set_attribute($attr_name, $attr_value){
		if (!is_null($attr_value)){
			$this->params[$attr_name] = $attr_value;
		}
		else{
			unset($this->params[$attr_name]);
		}
	}

	final protected function set_text($text){
		$this->text = $text;
	}

	public function add($child){
		array_push($this->children, $child);
	}
}

class tag_label extends tag{
	public function __construct($id, $text){
		parent::__construct('label');
		$this->set_attribute('for', $id);
		$this->set_text($text);
	}
}

class tag_input_text extends tag{
	public function __construct($class_id, $id, $name, $text){
		parent::__construct('input');
		$this->set_attribute('type', 'text');
		$this->set_attribute('class', $class_id);
		$this->set_attribute('id', $id);
		$this->set_attribute('name', $name);
		$this->set_attribute('value', $text);
	}
}

class tag_textarea extends tag{
	public function __construct($class_id, $id, $name, $text, $cols, $rows){
		parent::__construct('textarea');
		$this->set_attribute('class', $class_id);
		$this->set_attribute('id', $id);
		$this->set_attribute('name', $name);
		$this->set_attribute('cols', $cols);
		$this->set_attribute('rows', $rows);
		$this->set_text($text);
	}
}


class tag_input_file extends tag{
	public function __construct($class_id, $id, $name){
		parent::__construct('input');
		$this->set_attribute('type', 'file');
		$this->set_attribute('class', $class_id);
		$this->set_attribute('id', $id);
		$this->set_attribute('name', $name);
	}
}

class tag_span extends tag{
	public function __construct($style, $text){
		parent::__construct('span');
		$this->set_attribute('style', $style);
		$this->set_text($text);
	}
}

class tag_paragraph extends tag{
	public function __construct(){
		parent::__construct('p');
	}
}

class tag_paragraph_styled extends tag{
	public function __construct($style, $text){
		parent::__construct('p');
		$this->set_attribute('style', $style);
		$this->set_text($text);
	}
}

class tag_option extends tag{
	public function __construct($value, $text, $selected = FALSE){
		parent::__construct('option');
		$this->set_attribute('value', $value);
		if ($selected){
			$this->set_attribute('selected', 'selected');
		}
		$this->set_text($text);
	}
}

class tag_select extends tag{
	public function __construct($class_id, $id, $name){
		parent::__construct('select');
		$this->set_attribute('class', $class_id);
		$this->set_attribute('id', $id);
		$this->set_attribute('name', $name);
	}
}

function html_label($id, $text)
{
	return new tag_label($id, $text);
}

function html_input_text($class_name, $id, $name, $text=NULL){
	return new tag_input_text($class_name, $id, $name, $text);
}

function html_paragraph(){
	return new tag_paragraph();
}

?>