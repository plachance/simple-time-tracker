<?php

namespace SimpleTimeTracker;

/**
 * Class Util.
 * 
 * Class containing miscellaneous utility functions.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class Util
{
	/**
	 * Round a number to a fraction.
	 * @param float $number Number to round.
	 * @param int $denominator Fraction
	 * @param int $precision The optional number of decimal digits to round to.
	 * @return float The rounded number.
	 */
	public static function roundToFraction($number, $denominator = 1, $precision = null)
	{
		if($number === null)
		{
			return null;
		}

		$x = $number * $denominator;
		$x = round($x);
		$x = $x / $denominator;
		return $precision === null ? $x : round($x, $precision);
	}

}
