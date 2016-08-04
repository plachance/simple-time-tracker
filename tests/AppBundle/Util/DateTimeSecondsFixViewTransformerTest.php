<?php

namespace tests\AppBundle\Util;

use AppBundle\Util\DateTimeSecondsFixViewTransformer;
use PHPUnit\Framework\TestCase;

/**
 * Class DateTimeSecondsFixViewTransformerTest.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class DateTimeSecondsFixViewTransformerTest extends TestCase
{
	const VALUE = 'test';
	const DATETIME_WITHOUT_SECONDS = '2016-08-08T09:13';
	const DATETIME_WITHOUT_SECONDS_EXPECTED = '2016-08-08T09:13:00';
	const DATETIME_WITH_SECONDS = '2016-08-08T09:13:44';

	public function test_transform_returns_the_value_unchanged()
	{
		$transformer = new DateTimeSecondsFixViewTransformer();
		$result = $transformer->transform(self::VALUE);

		$this->assertEquals(self::VALUE, $result);
	}

	public function test_reverseTransform_adds_00_seconds_to_return_value_if_value_is_without_seconds()
	{
		$transformer = new DateTimeSecondsFixViewTransformer();
		$result = $transformer->reverseTransform(self::DATETIME_WITHOUT_SECONDS);

		$this->assertEquals(self::DATETIME_WITHOUT_SECONDS_EXPECTED, $result);
	}

	public function test_reverseTransform_returns_the_value_unchanged_if_value_contains_seconds()
	{
		$transformer = new DateTimeSecondsFixViewTransformer();
		$result = $transformer->reverseTransform(self::DATETIME_WITH_SECONDS);

		$this->assertEquals(self::DATETIME_WITH_SECONDS, $result);
	}

}
