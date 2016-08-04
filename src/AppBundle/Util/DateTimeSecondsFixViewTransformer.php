<?php

declare(strict_types = 1);

namespace AppBundle\Util;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class DateTimeSecondsFixViewTransformer.
 * 
 * Add missing seconds when a datetime-local HTML5 input is submitted.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class DateTimeSecondsFixViewTransformer implements DataTransformerInterface
{
	public function reverseTransform($value)
	{
		return preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $value) ? ($value . ':00') : $value;
	}

	public function transform($value)
	{
		return $value;
	}

}
