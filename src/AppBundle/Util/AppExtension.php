<?php

declare(strict_types = 1);

namespace AppBundle\Util;

use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Class AppExtension.
 * 
 * Twig extendions for the application.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class AppExtension extends Twig_Extension
{
	public function getFunctions()
	{
		return [
			new Twig_SimpleFunction('roundToFraction', [$this, 'roundToFraction']),
		];
	}

	public function getName()
	{
		return 'app_extension';
	}

	/**
	 * Round a number to a fraction.
	 *
	 * @param float $number Number to round.
	 * @param int $denominator Fraction
	 * @param int $precision The optional number of decimal digits to round to.
	 *
	 * @return float The rounded number.
	 */
	public function roundToFraction(float $number = null, int $denominator = 1,
		int $precision = null)
	{
		if($number === null)
		{
			return;
		}
		$x = $number * $denominator;
		$x = round($x);
		$x = $x / $denominator;

		return $precision === null ? $x : round($x, $precision);
	}

}
