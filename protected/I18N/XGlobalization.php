<?php

/**
 * Class XGlobalization.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class XGlobalization extends TGlobalization
{
	public function init($config)
	{
		parent::init($config);

		$culture = $this->getCulture();
		$locale = array($culture . '.UTF-8', $culture);
		setlocale(LC_COLLATE, $locale);
		setlocale(LC_CTYPE, $locale);
		setlocale(LC_MONETARY, $locale);
		setlocale(LC_TIME, $locale);
		setlocale(LC_MESSAGES, $locale);
	}

}
