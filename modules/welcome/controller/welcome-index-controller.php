<?php 
return \module\define(['welcome'], function($welcome){
	return function($action) use ($welcome){
		$welcome();
		return 'controllers system is working';
	};
});