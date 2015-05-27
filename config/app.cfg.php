<?php 

return array(
	'modules-dir' => 'modules',
	'modules' => array(
		'goods',
		'welcome' => [
			'action-controllers' => [
				'index'
			]
		],
		'admin' => [
			'action-controllers' => [
				'index'
			]
		],
		'layout'
	),
	'view' => [
		'default-layout' => 'modules'.DIRECTORY_SEPARATOR.'welcome'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'layout.phtml'
	]
);