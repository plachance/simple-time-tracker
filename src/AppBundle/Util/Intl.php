<?php

declare(strict_types=1);

namespace AppBundle\Util;

use DateTime;
use IntlDateFormatter;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class Intl.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class Intl
{
	/**
	 * @var TranslatorInterface
	 */
	protected $translator;

	/**
	 * @param TranslatorInterface $translator
	 */
	public function __construct(TranslatorInterface $translator)
	{
		$this->translator = $translator;
	}

	public function localizeDate(DateTime $date, string $dateFormat = 'medium',
		string $timeFormat = 'medium', string $locale = null, string $format = null,
		string $calendar = 'gregorian')
	{
		$formatValues = [
			'none' => IntlDateFormatter::NONE,
			'short' => IntlDateFormatter::SHORT,
			'medium' => IntlDateFormatter::MEDIUM,
			'long' => IntlDateFormatter::LONG,
			'full' => IntlDateFormatter::FULL,
		];

		$formatter = IntlDateFormatter::create(
				$locale == null ? $this->translator->getLocale() : $locale,
				$formatValues[$dateFormat], $formatValues[$timeFormat],
				$date->getTimezone()->getName(),
				'gregorian' === $calendar ? IntlDateFormatter::GREGORIAN : IntlDateFormatter::TRADITIONAL,
				$format
		);

		return $formatter->format($date->getTimestamp());
	}
}
