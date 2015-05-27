<?php 
return \module\define(['welcome', 'layout'], function($welcome, $layout){
	return function($action) use ($welcome, $layout){
		$filePath = APP_ROOT.'modules'.DIRECTORY_SEPARATOR.'welcome'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$action.'.phtml';
		
		if (file_exists($filePath)){
			$layoutPath = $filePath;
		} else {
			$layoutPath = 'modules'.DIRECTORY_SEPARATOR.'welcome'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'index.phtml';
		}
		return $layout(['_title'=>'Home page', '_content' => 'hello world'], $layoutPath);
	};
});