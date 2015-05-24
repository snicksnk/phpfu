<?php 
return module\define([], function(){
	$configPath = APP_ROOT.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'app.cfg.php';
	$appConfig = require_once($configPath);


	return function() use ($appConfig){
		return $appConfig;
	};
});