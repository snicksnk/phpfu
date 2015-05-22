<?php 
return module\define([], function(){
	$configPath = 'config'.DIRECTORY_SEPARATOR.'app.cfg.php';
	$appConfig = require_once($configPath);


	return function() use ($appConfig){
		return $appConfig;
	};
});