<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude([
		'bin',
		'var',
		'vendor',
		'web',
	])
    ->in(__DIR__)
;

return Symfony\CS\Config\Config::create()
    ->fixers([
		'-braces',
		'-indentation',
		'-concat_without_spaces',
		'-phpdoc_separation',
		'concat_with_spaces',
		'ordered_use',
		'phpdoc_order',
		'short_array_syntax',
	])
    ->finder($finder)
	->setUsingCache(true)
;