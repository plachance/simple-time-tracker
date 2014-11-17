<?php

/**
 * Development configuration file. Do not edit. To change these settings, do it 
 * in application.php. See application.default.php for example.
 */
$config = require 'application.dist.php';

$config['application']['mode'] = TApplicationMode::Debug;

$config['modules']['cache']['class'] = 'Application.Caching.XDummyCache';

$config['modules']['bd']['properties']['ConnectionClass'] = 'Application.Data.XDbConnection';
$config['modules']['doctrine']['properties']['SqlLoggerClass'] = 'SimpleTimeTracker\Data\PradoSQLLogger';
$config['modules']['doctrine']['properties']['CacheClass'] = 'Doctrine\Common\Cache\ArrayCache';

$config['modules']['mail']['properties']['Headers']['From'] = array('root@localhost' => 'SimpleTimeTracker');
$config['modules']['mail']['transport'] = array(
	'class' => 'Swift_SmtpTransport',
	'properties' => array(
		'Host' => 'smtp.gmail.com',
		'Port' => '465',
		'Encryption' => 'ssl',
		'Username' => '',
		'Password' => '',
	),
	'plugins' => array(
//		array(
//			'class' => 'Swift_Plugins_AntiFloodPlugin',
//			'properties' => array(
//				'Threshold' => 100,
//				'SleepTime' => 30,
//			),
//		),
//		array(
//			'class' => 'Swift_Plugins_ThrottlerPlugin',
//			'args' => array(0.1, Swift_Plugins_ThrottlerPlugin::MESSAGES_PER_SECOND),
//		),
		array(
			'class' => 'Swift_Plugins_LoggerPlugin',
			'logger' => array(
				'class' => 'Application.Util.SwiftMailerPradoLogger',
			),
		)
	),
);

$config['modules']['log']['routes']['Firebug'] = array(
	'class' => 'Application.Util.XFirebugLogRoute',
	'properties' => array(
		'Categories' => 'Application, Doctrine',
	),
);

return $config;