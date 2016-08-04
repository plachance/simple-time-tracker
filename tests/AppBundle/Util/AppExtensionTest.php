<?php

namespace tests\AppBundle\Util;

use AppBundle\Util\AppExtension;
use PHPUnit\Framework\TestCase;

/**
 * Class AppExtensionTest.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class AppExtensionTest extends TestCase
{
	public function test_roundToFraction_returns_null_if_number_is_null()
	{
		$ext = new AppExtension();
		$result = $ext->roundToFraction(null);

		$this->assertNull($result);
	}

	public function test_roundToFraction_round_half_up_to_specified_fraction()
	{
		$ext = new AppExtension();

		$result = $ext->roundToFraction(1.3749, 4);
		$this->assertEquals(1.25, $result);

		$result = $ext->roundToFraction(1.375, 4);
		$this->assertEquals(1.5, $result);

		$result = $ext->roundToFraction(-1.375, 4);
		$this->assertEquals(-1.5, $result);

		$result = $ext->roundToFraction(-1.3749, 4);
		$this->assertEquals(-1.25, $result);
	}

	public function test_roundToFraction_round_to_specified_precision_after_to_fraction()
	{
		$ext = new AppExtension();

		$result = $ext->roundToFraction(1.2, 3, 2);
		$this->assertEquals(1.33, $result);
	}

}
