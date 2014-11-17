<?php

return array(
	'authorization' => array(
		array(
			'action' => 'allow',
			'pages' => 'login, logout, recover',
		),
		array(
			'action' => 'allow',
			'users' => '@',
		),
		array(
			'action' => 'deny',
		),
	),
);