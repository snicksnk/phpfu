<?php 
return module\define(['config', 'router'], function($config, $router){


	$appConfig = $config();	

	$di = [];


	\module\addInitedModule($di, 'config', $config);
	\module\addInitedModule($di, 'router', $router);

	foreach ($appConfig['modules'] as $moduleNameOrIndex => $moduleNameOrConfig){

		if (is_array($moduleNameOrConfig)){
			$moduleName = $moduleNameOrIndex;
			$moduleConfig = $moduleNameOrConfig;
		} else {
			$moduleName = $moduleNameOrConfig;
			$moduleConfig = [];
		}



		\module\load($di, $moduleName, \module\getModuleFilePath($appConfig['modules-dir'], $moduleName));




		if (array_key_exists('action-controllers', $moduleConfig)){
			foreach ($moduleConfig['action-controllers'] as $controllerName) {
				$controllerModuleName = $moduleName.'-'.$controllerName.'-controller';
				\module\load($di, $controllerModuleName, \module\getModuleFilePath($appConfig['modules-dir'], $controllerModuleName, $moduleName));	
			}
		}		
	}


	\module\bootstrapAll($di);



	$result = $router($di);

	echo $result;

	return function() use ($appConfig){
		throw new \Exception('Application module is not callable');
	};


});	