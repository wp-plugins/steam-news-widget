<?php

namespace ssg_steam_news_core;

defined('ABSPATH') or die('No script kiddies please!');

class template_processor{

	const PARAM_PATTERN = '\{\{[a-z]+(\.[a-z]+)?\}\}';
	const SCRIPT_PATTERN = '\{\{\{(.|\n)*\}\}\}';
	const ATTR_FOREACH_IN = 'in';
	const ATTR_FOREACH_ITERATOR = 'iterator';

	private $params = array();

	public static function handle_xml_error($errno, $errstr, $errfile, $errline){
		
		if ($errno == E_WARNING
			&& (substr_count($errstr, 'DOMDocument::loadXML()') > 0)){
			throw new \DOMException($errstr);
		}
		return FALSE;
	}

	public function run($template, $params){

		if (!class_exists('\DOMDocument')){
			throw new \Exception('DOMDocument class is missing. Make sure all necessary XML libraries are installed.');
		}

		$this->params = $params;
		$result_str = '';

		/* 
		 * XML does not support multiple root nodes.
		 * Wrap the template to make sure this condition is always met.
		 */
		$template_wrapped = sprintf('<xml>%s</xml>', $template); 

		set_error_handler(
			array('\ssg_steam_news_core\template_processor', 'handle_xml_error'));

		try{

			$dom = new \DOMDocument;
			$dom->loadXML($template_wrapped);

			$xml_node = $dom->childNodes->item(0);

			if (!$xml_node->hasChildNodes()){
				throw new \Exception('Empty template.');
			}

			$this->process_node($xml_node, $xml_node);

			foreach($xml_node->childNodes as $node){
				$result_str .= $dom->saveXML($node);
			}
		}
		catch (\Exception $e){
			restore_error_handler();
			throw $e;
		}
		restore_error_handler();
		return $result_str;
	}

	private function process_node($parent_node, $node){

		if ($node->nodeName === 'foreach'){
			$this->process_foreach_loop($parent_node, $node);
		}
		else{
			if ($node->hasAttributes()){
				$this->process_attributes($node);
			}

			if ($node->hasChildNodes()){
				foreach($node->childNodes as $child_node){
					$this->process_node($node, $child_node);
				}
			}
			$this->expand_text_node_inline_params($node);
		}
	}

	private static function is_template_attribute($value){

		return 1 === preg_match('/^'.self::PARAM_PATTERN.'$/', $value);
	}

	private static function is_inline_script($value){

		return 1 === preg_match('/^'.self::SCRIPT_PATTERN.'$/', $value);
	}

	private function extract_parameter_name($attr_value){

		if (self::is_template_attribute($attr_value)){
			$param = substr($attr_value, 2, strlen($attr_value) - 4);
			return $param;
		}

		if (preg_match('/\{|\}/', $attr_value)){
			throw new \Exception('Malformed parameter "'.$attr_value.'".');
		}
		return FALSE;
	}

	private function extract_parameter_value($param_name){

		$name_parts = explode('.', $param_name);
		$params = &$this->params;

		foreach($name_parts as $part){

			if (!is_array($params) or !array_key_exists($part, $params)){
				throw new \Exception('Undefined template parameter: "'.$param_name.'".');
			}
			$params = &$params[$part];
		}
		return $params;
	}

	private function process_script($param){
		$script = substr($param, 3, strlen($param) - 6);
		$preped_script = $this->expand_inline_params($script);
		$result = @eval($preped_script);
		if (FALSE === $result){
			throw new \Exception(sprintf('Failed to execute the script: "%s".', $preped_script));
		}
		return $result;
	}

	private function process_parameter($param){

		$processed_param = $param;
		if (self::is_inline_script($param)){
			$processed_param = $this->process_script($param);
		}
		else{
			$param_name = $this->extract_parameter_name($param);
			if (FALSE !== $param_name)
			{
				$processed_param = $this->extract_parameter_value($param_name);
			}
		}
		return $processed_param;
	}

	private function process_attribute($attribute){

		$attribute->nodeValue = $this->process_parameter($attribute->nodeValue);
	}

	private function process_attributes($node){

		foreach($node->attributes as $attribute){

			$this->process_attribute($attribute);
		}
	}

	private function process_foreach_loop($parent_node, $node){

		if (!$node->hasChildNodes()){
			throw new \Exception('Empty foreach loop.');
		}

		if (!$node->hasAttribute(self::ATTR_FOREACH_IN)){
			throw new \Exception('Incorrect foreach loop: missing "'.self::ATTR_FOREACH_IN.'".');
		}

		if (!$node->hasAttribute(self::ATTR_FOREACH_ITERATOR)){
			throw new \Exception('Incorrect foreach loop: missing "'.self::ATTR_FOREACH_ITERATOR.'".');
		}

		$attribute_in = $node->getAttribute(self::ATTR_FOREACH_IN);
		$item_array_name = $this->extract_parameter_name($attribute_in);

		$item_array = $this->extract_parameter_value($item_array_name);

		if (!is_array($item_array)){
			throw new \Exception('Incorrect foreach loop: "'.$item_array_name.'" is not an array.');
		}

		$attribute_iterator = $node->getAttribute(self::ATTR_FOREACH_ITERATOR);
		$iterator_name = $this->extract_parameter_name($attribute_iterator);

		/* The iterator should not intervene with the template's input parameters. */
		if (array_key_exists($iterator_name, $this->params)){
			throw new \Exception('Incorrect foreach loop: iterator "'.$iterator_name.'" intervenes with other template parameters.');
		}	

		foreach($item_array as $item){

			/* register iterator parameter */
			$this->params[$iterator_name] = $item;

			foreach($node->childNodes as $child){

				$cloned_child = $child->cloneNode(TRUE);
				$this->process_node($node, $cloned_child);
				$parent_node->appendChild($cloned_child);
			}
			unset($this->params[$iterator_name]);
		}
		/* remove the loop from the tree */
		$parent_node->removeChild($node);
	}

	private function expand_inline_params($value){

		$new_value = preg_replace_callback(
			'/('.self::PARAM_PATTERN.'|'.self::SCRIPT_PATTERN.')/',
			array($this, 'expand_inline_params_callback'),
			$value);
		return $new_value;
	}

	private function expand_text_node_inline_params($node){

		if ($node->nodeType === XML_TEXT_NODE){
			$node->nodeValue = $this->expand_inline_params($node->nodeValue);
		}
	}

	private function expand_inline_params_callback($matches){

		$replacement = $this->process_parameter($matches[0]);
		return $replacement;
	}
}

?>
