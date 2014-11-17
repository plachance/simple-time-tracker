<?php

namespace SimpleTimeTracker;

/**
 * Class DateTime.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class DateTime extends \DateTime
{
	const HTML5 = "Y-m-d\TH:i:s";

	/**
	 * @return string HTML5 week string.
	 */
	public function toWeekString()
	{
		return $this->format('Y') . '-W' . str_pad($this->format('W'), 2, '0', STR_PAD_LEFT);
	}
}
