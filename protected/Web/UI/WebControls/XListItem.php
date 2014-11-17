<?php

/**
 * Class XListItem.
 * 
 * TListItem that allow null/empty values.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class XListItem extends TListItem
{
	/**
	 * @var string value of the item
	 */
	private $_value;

	/**
	 * @var string text of the item
	 */
	private $_text;

	/**
	 * @return string text of the item
	 */
	public function getText()
	{
		return $this->_text === '' ? $this->_value : $this->_text;
	}

	/**
	 * @param string text of the item
	 */
	public function setText($value)
	{
		$this->_text = TPropertyValue::ensureString($value);
	}

	/**
	 * @return string value of the item
	 */
	public function getValue()
	{
		return $this->_value;
	}

	/**
	 * @param string value of the item
	 */
	public function setValue($value)
	{
		$this->_value = TPropertyValue::ensureString($value);
	}

}
