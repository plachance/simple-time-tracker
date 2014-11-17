<?php

Prado::using('System.I18N.TDateFormat');

/**
 * Class XDateFormat.
 *
 * Fix TDateFormat to render nothing if Value and DefaultText are empty.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class XDateFormat extends TDateFormat
{
	public function getValue()
	{
		return $this->getViewState('Value', '');
	}

	protected function getFormattedDate()
	{
		$value = $this->getValue();
		return empty($value) ? $this->getDefaultText() : parent::getFormattedDate();
	}

}
