<?php

declare(strict_types = 1);

namespace AppBundle\Util;

use DateTime as SplDateTime;

/**
 * Class DateTime.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class DateTime extends SplDateTime
{
	/**
	 * Date format for PGSQL.
	 */
	const PGSQL_DATE = 'Y-m-d';

	/**
	 * DateTime format for PGSQL.
	 */
	const PGSQL_TIMESTAMP = 'Y-m-d H:i:s';

}
