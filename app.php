<?php  
namespace module{

	function bootstrap(&$di, $modulesDir, $name){
		$moduleDefinition = include_once($modulesDir.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$name.'.php');
		$moduleDis = $moduleDefinition($di);
		$di[$name] = $moduleDis;
	}

	function define($moduleDependencies, $moduleDefinition){

		$definer =  function ($di) use ($moduleDependencies, $moduleDefinition) {
			$dependencies = [];

			foreach ($moduleDependencies as $dependencieName) {

				if (!array_key_exists($dependencieName, $di)){
					throw new \Exception("Module {$dependencieName} is not defined");
				}
				$dependencies[] = $di[$dependencieName];
			}
			return call_user_func_array($moduleDefinition, $dependencies);
		};

		return $definer;
	}

}
namespace {
	
	$appConfig = require_once(__DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'app.cfg.php');
	$di = [];
	$modulesDir = __DIR__.DIRECTORY_SEPARATOR.'modules';

	foreach ($appConfig['modules'] as $moduleName) {
		\module\bootstrap($di, $modulesDir ,$moduleName);
	}

}