<?php

declare(strict_types=1);
use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;

/* @var $loader ClassLoader */
$loader = require __DIR__ . '/../vendor/autoload.php';

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

return $loader;
