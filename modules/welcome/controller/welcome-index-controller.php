<?php 
return \module\define(['welcome'], function($welcome){
	return function() use ($welcome){
		$welcome();
		return 'controllers system is working';
	};
});