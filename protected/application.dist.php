<?php

/**
 * Production configuration file. Do not edit. To change these settings, do it 
 * in application.php. See application.default.php for example.
 */
return array(
	'application' => array(
		'id' => 'SimpleTimeTracker',
		'mode' => TApplicationMode::Normal,
	),
	'paths' => array(
		'using' => array(
			'System.I18N.*',
			'Application.I18N.*',
			'Application.Web.UI.*',
			'Application.Web.UI.WebControls.*',
		),
	),
	'modules' => array(
		'cache' => array(
			'class' => 'System.Caching.TAPCCache',
		),
		'session' => array(
			'class' => 'System.Web.THttpSession',
			'properties' => array(
				'CookieMode' => THttpSessionCookieMode::Only,
				'AutoStart' => true,
			),
		),
		'request' => array(
			'class' => 'System.Web.THttpRequest',
			'properties' => array(
				'EnableCookieValidation' => true,
				'UrlManager' => 'friendly-url',
			),
		),
		'friendly-url' => array(
			'class' => 'System.Web.TUrlMapping',
			'properties' => array(
				'EnableCustomUrl' => true,
				'UrlPrefix' => '/',
			),
			'urls' => array(
				array('properties' => array('ServiceParameter' => 'task.*', 'pattern' => 'task/{id}/{*}', 'CaseSensitive' => false, 'parameters.id' => '\d+')),
				array('properties' => array('ServiceParameter' => 'task.*', 'pattern' => 'task/{*}', 'CaseSensitive' => false)),
				array('properties' => array('ServiceParameter' => 'task.current', 'pattern' => '', 'CaseSensitive' => false)),
				array('properties' => array('ServiceParameter' => '*', 'pattern' => '{*}', 'CaseSensitive' => false)),
			),
		),
		'security' => array(
			'class' => 'System.Security.TSecurityManager',
		),
		'globalization' => array(
			'class' => 'Application.I18N.XGlobalization',
			'properties' => array(
				'DefaultCulture' => 'en_US',
				'Culture' => 'en_US',
			),
		),
		'bd' => array(
			'class' => 'System.Data.TDataSourceConfig',
			'database' => array(
				'ConnectionString' => 'pgsql:host=localhost;dbname=simpletimetracker',
				'Username' => '',
				'Password' => '',
			),
		),
		'doctrine' => array(
			'class' => 'Application.Data.XDoctrine',
			'properties' => array(
				'MetadataPaths' => 'Entities/Metadata',
				'EntityNamespaces' => 'SimpleTimeTracker\\Entities\\',
				'ConnectionID' => 'bd',
				'CacheClass' => 'Doctrine\Common\Cache\ApcCache',
			),
			'CustomStringFunction' => array(
				'ILIKE' => 'SimpleTimeTracker\Data\Query\AST\ILikeFunction'
			),
		),
		'log' => array(
			'class' => 'System.Util.TLogRouter',
			'routes' => array(),
		),
		'auth' => array(
			'class' => 'System.Security.TAuthManager',
			'properties' => array(
				'UserManager' => 'users',
				'LoginPage' => 'login',
				'AllowAutologin' => 'true',
			),
		),
		'users' => array(
			'class' => 'Application.Security.XUserManager',
			'properties' => array(
			),
		),
		'mail' => array(
			'class' => 'Application.Util.SwiftMailer',
			'properties' => array(
				'Headers' => array(
					'From' => array('noreply@simpletimetracker.org', 'SimpleTimeTracker'),
				),
			),
			'transport' => array(
				'class' => 'Swift_NullTransport',
			),
		),
	),
	'services' => array(
		'page' => array(
			'class' => 'Application.Web.Services.XPageService',
			'properties' => array(
				'DefaultPage' => 'task.current',
			),
			'modules' => array(
				'theme' => array(
					'class' => 'System.Web.UI.TThemeManager',
				),
			),
			'pages' => array(
				'properties' => array(
					'MasterClass' => 'Application.Web.UI.Templates.MasterTemplate',
					'StyleSheetTheme' => 'default',
				),
			),
		),
	),
	'parameters' => array(
	),
);
