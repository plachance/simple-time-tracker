<?php

require 'vendor/autoload.php';

//// The following directory checks may be removed if performance is required
//$basePath=dirname(__FILE__);
//$assetsPath=$basePath.'/assets';
//$runtimePath=$basePath.'/protected/runtime';
//
//if(!is_writable($assetsPath))
//	die("Please make sure that the directory $assetsPath is writable by Web server process.");
//if(!is_writable($runtimePath))
//	die("Please make sure that the directory $runtimePath is writable by Web server process.");

$application = new TApplication('protected', false, TApplication::CONFIG_TYPE_PHP);
$application->run();