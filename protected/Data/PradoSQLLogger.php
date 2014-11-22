<?php

namespace SimpleTimeTracker\Data;

use Doctrine\DBAL\Logging\SQLLogger;
use PDO;
use Prado;
use TLogger;
use TNotSupportedException;
use TPropertyValue;

/**
 * Class PradoSQLLogger.
 * 
 * SQL Query logger for Doctrine through Prado.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class PradoSQLLogger implements SQLLogger
{
	const DATETIME_FORMAT = 'Y-m-d H:i:s';

	private $level;
	private $category;

	public function getLevel()
	{
		return $this->level;
	}

	public function setLevel($value)
	{
		$this->level = TPropertyValue::ensureNullIfEmpty($value);
	}

	public function getCategory()
	{
		return $this->category;
	}

	public function setCategory($value)
	{
		$this->category = TPropertyValue::ensureNullIfEmpty($value);
	}

	public function __construct($level = TLogger::DEBUG, $category = 'Doctrine')
	{
		$this->setLevel($level);
		$this->setCategory($category);
	}

	public function startQuery($sql, array $params = null, array $types = null)
	{
		if(is_array($params))
		{
			foreach($params as $key => $value)
			{
				if($value === null)
				{
					$value = 'NULL';
				}
				else
				{
					switch($types[$key])
					{
						case PDO::PARAM_INT:
						case 'integer':
							break;

						case PDO::PARAM_STR:
						case 'text':
							$value = "'$value'";
							break;

						case 'datetime':
							$value = $value->format(self::DATETIME_FORMAT);
							break;

						case 'json_array':
							$value = "'" . json_encode($value) . "'";
							break;

						case 'boolean':
							$value = $value ? 'true' : 'false';
							break;

						default:
							throw new TNotSupportedException(Prado::localize('Unsupported type "{type}".', array('type' =>  $types[$key])));
					}
				}

				if(is_int($key))
				{
					$sql = preg_replace('/\?/', $value, $sql, 1);
				}
				else
				{
					$sql = preg_replace("/:{$key}( |\\W|$)/", $value . '$1', $sql);
				}
			}
		}

		Prado::log($sql, $this->getLevel(), $this->getCategory());
	}

	public function stopQuery()
	{
		
	}

}
