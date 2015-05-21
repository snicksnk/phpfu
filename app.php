<?php  
namespace module{

	/**
	* TODO Add Lazy loading
	*/
	function load(&$di, $name, $moduleDefinition) {
		$di[$name] = [
			'definition' => $moduleDefinition;
			'module' => null,
			'isInited' => false
		];
	}

	function define($moduleDependencies, $moduleDefinition){

		$definer =  function ($di) use ($moduleDependencies, $moduleDefinition) {
			$dependencies = [];

			foreach ($moduleDependencies as $dependencieName) {

				if (!array_key_exists($dependencieName, $di)){
					throw new \Exception("Module {$dependencieName} is not defined");
				}
				
				bootsrap($di, $di[$dependencieName]);
				$dependencies[] = $di[$dependencieName]['module'];
			}
			return call_user_func_array($moduleDefinition, $dependencies);
		};

		return $definer;
	}

	function bootstrapAll(&$di) {
		foreach ($di as &$moduleData) {
			bootsrap($di, $moduleData);
		}

	}

	function bootsrap($di, &$moduleData){
		if ($moduleData['isInited'] === false){
			$moduleData['module'] = $moduleData['definition']($di);
			$moduleData['isInited'] = true;
		}
	}

}

namespace {


	$configPath = __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'app.cfg.php';
	$appConfig = require_once($configPath);
	$di = [];

	$modulesDir = __DIR__.DIRECTORY_SEPARATOR.'modules';

	foreach ($appConfig['modules'] as $moduleName) {
		$moduleDefinition = include_once($modulesDir.DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR.$moduleName.'.php');
		\module\load($di, $moduleName, $moduleDefinition);
	}

	\module\bootstrapAll($di);

}