<?php

namespace ssg_steam_news_core;

defined('ABSPATH') or die('No script kiddies please!');

class url_fetcher{

	protected function is_curl_supported(){
		return in_array('curl', get_loaded_extensions ())
			and function_exists('curl_init')
			and function_exists('curl_exec')
			and function_exists('curl_multi_init')
			and function_exists('curl_multi_exec');
	}

	public function fetch($url_array){

		require_once(__DIR__.'/swt-numbers.php');

		if (!$this->is_curl_supported()){
			throw new \Exception('The required cURL extension is not available.');
		}

		$multi_handle = curl_multi_init();
		if (!$multi_handle){
			throw new \Exception('curl_multi_init() call failed.');
		}

		$curl_array = array();

		foreach($url_array as $url){
			$ch = curl_init();
			$options = array(
				CURLOPT_SSL_VERIFYPEER => FALSE,
				CURLOPT_SSL_VERIFYHOST => FALSE,
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_URL => $url,
			);
			curl_setopt_array($ch, $options);
			curl_multi_add_handle($multi_handle, $ch);
			array_push($curl_array, $ch);
		}

		$mrc = NULL;
		$active = NULL;
		do{
			$mrc = curl_multi_exec($multi_handle, $active);
		}
		while($mrc == CURLM_CALL_MULTI_PERFORM);

		while($active && $mrc == CURLM_OK){

			if (curl_multi_select($multi_handle) == -1){
				usleep(1000);
			}

			do{
				$mrc = curl_multi_exec($multi_handle, $active);
			}
			while($mrc == CURLM_CALL_MULTI_PERFORM);
		}

		$results = array();
		foreach($curl_array as $query_index => $ch){
			$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if (is_integer_in_range($status_code, 200, 206)){
				array_push($results, curl_multi_getcontent($ch));
				curl_multi_remove_handle($multi_handle, $ch);
				curl_close($ch);
			}
			else{
				if ($status_code){
					$error_msg = sprintf(
						'HTTP request "%s" failed with error code "%d".',
						$url_array[$query_index], $status_code);
				}
				else{
					$error_msg = sprintf('HTTP request "%s" failed.', $url_array[$query_index]);
				}
				throw new \Exception($error_msg);
			}
		}
		curl_multi_close($multi_handle);
		return $results;
	}
}

?>
