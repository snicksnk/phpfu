<?php 
namespace render {
	return \module\define(["config"], function($config){
		
		$render = function ($templatePath, $vars){
			extract($vars);
			include($templatePath);
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		};


		return function($vars, $customLayout = null) use ($config, $render){
			
			if (!$customLayout){
				$layout = $config()['view']['default-layout'];
			} else {
				$layout = $customLayout;
			}

			$path = APP_ROOT.DIRECTORY_SEPARATOR.$layout;
			
			$result = $render($path, $vars);
			return $result;
		};
	});
}