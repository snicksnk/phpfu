<?php 
return \module\define(['welcome', 'layout', 'storage'], function($welcome, $layout, $storage){
	session_id() || session_start();
	return function($action) use ($welcome, $layout, $storage){
		$filePath = 'modules'.DIRECTORY_SEPARATOR.'welcome'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$action.'.phtml';
		
		//$filePath = str_replace(DIRECTORY_SEPARATOR, '-', $filePath);

		if (file_exists(APP_ROOT.DIRECTORY_SEPARATOR.$filePath)){
			$layoutPath = $filePath;
		} else {
			$layoutPath = 'modules'.DIRECTORY_SEPARATOR.'welcome'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'index.phtml';
		}
		$isAdmin = false;
		if (array_key_exists('isAdmin', $_SESSION)){
			$isAdmin = $_SESSION['isAdmin'];
		}

		return $layout(['_title'=>'Home page', 'isAdmin' => $isAdmin ,'blocks' => $storage,'_content' => 'hello world'], $layoutPath);
	};
});