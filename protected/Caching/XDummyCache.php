<?php

/**
 * Class XDummyCache.
 *
 * Dummy cache development.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class XDummyCache extends TCache
{
	private $cache = array();

	protected function addValue($key, $value, $expire)
	{
		if(isset($this->cache[$key]))
		{
			return false;
		}
		$this->cache[$key] = $value;
		return true;
	}

	protected function deleteValue($key)
	{
		unset($this->cache[$key]);
		return true;
	}

	protected function getValue($key)
	{
		return isset($this->cache[$key]) ? $this->cache[$key] : false;
	}

	protected function setValue($key, $value, $expire)
	{
		$this->cache[$key] = $value;
		return true;
	}

}
