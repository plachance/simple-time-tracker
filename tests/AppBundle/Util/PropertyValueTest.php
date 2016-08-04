<?php

namespace tests\AppBundle\Util;

use AppBundle\Util\PropertyValue;
use PHPUnit\Framework\TestCase;

/**
 * Class PropertyValueTest.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class PropertyValueTest extends TestCase
{
	const EMPTY_STRING = '';
	const BLANK_STRING = '   ';
	const NOT_EMPTY_STRING = 'test';

	public function test_ensureNullIfEmpty_returns_null_if_empty()
	{
		$result = PropertyValue::ensureNullIfEmpty(self::EMPTY_STRING);

		$this->assertNull($result);
	}

	public function test_ensureNullIfEmpty_returns_value_if_blank()
	{
		$result = PropertyValue::ensureNullIfEmpty(self::BLANK_STRING);

		$this->assertEquals(self::BLANK_STRING, $result);
	}

	public function test_ensureNullIfEmpty_returns_value_if_not_empty()
	{
		$result = PropertyValue::ensureNullIfEmpty(self::NOT_EMPTY_STRING);

		$this->assertEquals(self::NOT_EMPTY_STRING, $result);
	}

	/**
	 * @depends test_ensureNullIfEmpty_returns_null_if_empty
	 * @depends test_ensureNullIfEmpty_returns_value_if_blank
	 * @depends test_ensureNullIfEmpty_returns_value_if_not_empty
	 */
	public function test_ensureNullIfWhiteString_returns_null_if_empty()
	{
		$result = PropertyValue::ensureNullIfWhiteString(self::EMPTY_STRING);

		$this->assertNull($result);
	}

	/**
	 * @depends test_ensureNullIfEmpty_returns_null_if_empty
	 * @depends test_ensureNullIfEmpty_returns_value_if_blank
	 * @depends test_ensureNullIfEmpty_returns_value_if_not_empty
	 */
	public function test_ensureNullIfWhiteString_returns_null_if_blank()
	{
		$result = PropertyValue::ensureNullIfWhiteString(self::BLANK_STRING);

		$this->assertNull($result);
	}

	/**
	 * @depends test_ensureNullIfEmpty_returns_null_if_empty
	 * @depends test_ensureNullIfEmpty_returns_value_if_blank
	 * @depends test_ensureNullIfEmpty_returns_value_if_not_empty
	 */
	public function test_ensureNullIfWhiteString_returns_value_if_not_empty()
	{
		$result = PropertyValue::ensureNullIfWhiteString(self::NOT_EMPTY_STRING);

		$this->assertEquals(self::NOT_EMPTY_STRING, $result);
	}

}
