<?php  
namespace module{

	/**
	* TODO Add Lazy loading
	*/
	function load(&$di, $name, $path) {
		$di[$name] = [
			'path' => $path,
			'definition' => null,
			'module' => null,
			'isInited' => false
		];
	}

	function addInitedModule(&$di, $name, $module) {
		$di[$name] = [
			'path' => null,
			'definition' => null,
			'module' => $module,
			'isInited' => true
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
			$moduleData['definition'] = include_once($moduleData['path']);
			$moduleData['module'] = $moduleData['definition']($di);
			$moduleData['isInited'] = true;
		}
	}

	function getModuleFilePath($modulesDir, $moduleName) {
		return $modulesDir.DIRECTORY_SEPARATOR.
		$moduleName.DIRECTORY_SEPARATOR.
		$moduleName.'.php';
	}

}

namespace {

	$configPath = __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'app.cfg.php';
	$di = [];
	$modulesDir = __DIR__.DIRECTORY_SEPARATOR.'modules';
	\module\load($di, 'config', \module\getModuleFilePath($modulesDir, 'config'));
	\module\load($di, 'application', \module\getModuleFilePath($modulesDir, 'application'));
	\module\bootstrapAll($di);

}