<?php

declare(strict_types = 1);

namespace AppBundle\Util;

/**
 * Class PropertyValue.
 * 
 * Utility functions to deals with inputs.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class PropertyValue
{
	public static function ensureNullIfEmpty($value)
	{
		return empty($value) ? null : $value;
	}

	public static function ensureNullIfWhiteString($value)
	{
		return self::ensureNullIfEmpty(trim((string) $value));
	}

}
