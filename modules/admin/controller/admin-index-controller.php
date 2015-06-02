<?php 
return \module\define(['storage', 'layout'],  function($storage, $layout){
	return function($action) use ($storage, $layout){

		switch ($action) {
			case 'save':
				if ($_GET['id'] && $_POST['html']){
					$id = $_GET['id'];
					$html = $_POST['html'];
					$storage($id, $html);	
				}

				break;
			case 'logout':
					session_id() || session_start();
					$_SESSION['isAdmin'] = false;
					header('location: index.php');
			break;
			case 'login':

				$isOk = false;
				$error = false;
				if ($_POST['login'] && $_POST['password']){
					if ($_POST['login'] === 'admin' && $_POST['password'] === 'dMra12093'){
						$isOk = true;
						session_id() || session_start();
						$_SESSION['isAdmin'] = true;
						header('location: index.php');
					} else {
						$error = true;
					}
				}
				$filePath = 'modules'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$action.'.phtml';
		
				//$filePath = str_replace(DIRECTORY_SEPARATOR, '-', $filePath);
				if (file_exists(APP_ROOT.DIRECTORY_SEPARATOR.$filePath)){
					$layoutPath = $filePath;
				} else {
					$layoutPath = 'modules'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'index.phtml';
				}


				return $layout(['_title'=>'Home page', '_isError'=>$error ,'blocks' => $storage,'_content' => 'hello world'], $layoutPath);
			

				break;
			default:
				# code...
				break;
		}

		return $action.' admin module';
	};
});