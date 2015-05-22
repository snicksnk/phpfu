<?php 
return module\define(['config'], function($config){


	$appConfig = $config();	

	$di = [];


	\module\addInitedModule($di, 'config', $config);

	foreach ($appConfig['modules'] as $moduleName) {
		\module\load($di, $moduleName, \module\getModuleFilePath($appConfig['modules-dir'], $moduleName));
	}

	\module\bootstrapAll($di);

	return function() use ($appConfig){
		throw new \Exception('Application module is not callable');
	};


});	