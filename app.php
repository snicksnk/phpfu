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
			if (!is_callable($moduleDefinition)){
				var_dump($moduleData);
				throw new \Exception("Can't init module.");
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
			$moduleData['definition'] = include($moduleData['path']);

			if (!is_callable($moduleData['definition'])){
				var_dump($moduleData);
				throw new \Exception("Can't init module.");
			}
			$moduleData['module'] = $moduleData['definition']($di);
			$moduleData['isInited'] = true;
		}
	}

	function getModuleFilePath($modulesDir, $moduleName, $parentModule = null) {
		

		$nameParts = explode('-', $moduleName);

		if (count($nameParts) > 1){
			$type = $nameParts[count($nameParts)-1];
		} else {
			$type = 'module';
		}
		
		if ($type !== 'module'){
			$typeDir = $type.DIRECTORY_SEPARATOR;
		} else {
			$typeDir = DIRECTORY_SEPARATOR;
		}

		if ($parentModule){
			$parentModuleDir = $modulesDir.DIRECTORY_SEPARATOR.$parentModule;
			return $parentModuleDir.DIRECTORY_SEPARATOR.$typeDir.$moduleName.'.php';
		}

		return $modulesDir.DIRECTORY_SEPARATOR.
		$moduleName.$typeDir.
		$moduleName.'-'.$type.'.php';
	}

	function get($di, $moduleName){
		bootsrap($di, $di[$moduleName]);
		return $di[$moduleName]['module'];
	}


}

namespace {

	define('APP_ROOT', __DIR__);

	$configPath = __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'app.cfg.php';
	$di = [];
	$modulesDir = __DIR__.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'snicksnk'.
	DIRECTORY_SEPARATOR.'phpfu'.DIRECTORY_SEPARATOR;
	\module\load($di, 'config', \module\getModuleFilePath($modulesDir, 'config'));
	\module\load($di, 'application', \module\getModuleFilePath($modulesDir, 'application'));
	\module\load($di, 'router', \module\getModuleFilePath($modulesDir, 'router'));
	


	\module\bootstrapAll($di);



}