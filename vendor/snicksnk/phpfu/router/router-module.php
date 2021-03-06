<?php 
return module\define(['config'], function($config){

	function getParam($index, $defaultValue, $sourceArray){
		if (array_key_exists($index, $sourceArray)){
			return $sourceArray[$index];
		} else {
			return $defaultValue;
		}
	}	

	return function($di){
		$module = getParam('module', 'welcome', $_GET);
		$controller = getParam('controller', 'index', $_GET);
		$action = getParam('action', 'index', $_GET);

		$controllerModuleName = $module.'-'.$controller.'-controller';


		if(array_key_exists($controllerModuleName, $di)){

		} else {
			throw new \Exception("Controller {$controllerModuleName} is not finded");
		}

		$currentController = module\get($di, $controllerModuleName);
	

		return $currentController($action);
	};
});