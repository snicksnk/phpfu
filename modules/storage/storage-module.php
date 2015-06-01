<?php 
return \module\define([],  function(){
	$pathToFile = APP_ROOT.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'data.json';

	return function($key = null, $value = null) use ($pathToFile){
		if (!$key){
			return json_decode(file_get_contents($pathToFile), true);
		} elseif ($key && !$value) {
			$prevData = json_decode(file_get_contents($pathToFile), true);
			if (array_key_exists($key, $prevData)){
				return $prevData[$key];	
			} else {
				return '&nbsp;';
			}
		}
		elseif ($key && $value) {

			$prevData = json_decode(file_get_contents($pathToFile), true);
			
			echo file_get_contents($pathToFile);
			$prevData[$key] = $value;


			return file_put_contents($pathToFile, json_encode($prevData));
		}
	};
});