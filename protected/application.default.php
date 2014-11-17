<?php

/**
 * Example configuration file.
 * 
 * Copy this file to application.php.
 */

/**
 * For production settings, load application.dist.php and for development settings, load application.dev.php.
 */
$config = require 'application.dist.php';

/**
 * Database configuration.
 */
$config['modules']['bd']['database']['ConnectionString'] = 'pgsql:host=localhost;dbname=simpletimetracker';
$config['modules']['bd']['database']['Username'] = '';
$config['modules']['bd']['database']['Password'] = '';

/**
 * If the application isn't installed a the root, configure this.
 */
$config['modules']['friendly-url']['properties']['UrlPrefix'] = '/SimpleTimeTracker/';


/**
 * Swiftmailer settings.
 */
$config['modules']['mail']['properties']['Headers']['From'] = array('noreply@simpletimetracker.org' => 'SimpleTimeTracker');
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

return $config;