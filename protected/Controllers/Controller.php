<?php

namespace SimpleTimeTracker\Controllers;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\OrderBy;
use Prado;
use TApplicationComponent;
use TDbConnection;

/**
 * Class Controller.
 * 
 * Base class for all controllers.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class Controller extends TApplicationComponent
{
	/**
	 * Date format for PGSQL
	 */
	const PGSQL_DATE_FORMAT = 'Y-m-d';

	/**
	 * DateTime format for PGSQL.
	 */
	const PGSQL_TIMESTAMP_FORMAT = 'Y-m-d H:i:s';

	/**
	 * Entities namespace.
	 */
	const ENTITY_NS = 'SimpleTimeTracker\Entities\\';

	/**
	 * @return EntityManager
	 */
	protected function getEntityManager()
	{
		return $this->getApplication()->getModule('doctrine')->getEntityManager();
	}

	/**
	 * @return TDbConnection
	 */
	protected function getDbConnection()
	{
		return Prado::getApplication()->getModule('bd')->getDbConnection();
	}

	/**
	 * Create a Doctrine OrderBy from the specified Prado's sort expression.
	 * @param string $sortExpr Prado Sort Expression string.
	 * @return null|OrderBy Doctrine OrderBy created or null if $sortExpr is null.
	 */
	protected function getOrderBy($sortExpr)
	{
		if($sortExpr == null)
		{
			return null;
		}

		$orderBy = new OrderBy();
		$sortExpr = explode(',', $sortExpr);
		foreach($sortExpr as $expr)
		{
			if(($pos = stripos($expr, ' asc')) !== false || ($pos = stripos($expr, ' desc')) !== false)
			{
				$sort = substr($expr, 0, $pos);
				$order = substr($expr, $pos + 1);
				$orderBy->add(trim($sort), trim($order));
			}
			else
			{
				$orderBy->add(trim($expr));
			}
		}
		return $orderBy;
	}

}
