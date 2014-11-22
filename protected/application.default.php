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
 * Application mode.
 * 
 * -TApplicationMode.Off to turn the site offline.
 * -TApplicationMode.Normal mode is mainly used during production stage. Exception information will only be recorded in system error logs. The cache is ensured to be up-to-date if it is enabled.
 * -TApplicationMode.Performance mode is similar to Normal mode except that it does not ensure the cache is up-to-date.
 */
$config['application']['mode'] = TApplicationMode::Performance;

/**
 * Cache configuration.
 * 
 * Available cache classes :
 * -System.Caching.TAPCCache (default)
 * -System.Caching.TDbCache
 * -System.Caching.TEACache
 * -System.Caching.TMemCache
 * -Application.Caching.XDummyCache
 * 
 * Some cache chasses require additionnal configuration.
 * @see http://www.pradosoft.com/docs/manual/package-System.Caching.html
 */
$config['modules']['cache']['class'] = 'System.Caching.TAPCCache';

/**
 * I18N configuration.
 */
$config['modules']['globalization']['properties']['Culture'] = 'fr_CA';

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

//Logging

return $config;