<?php

/**
 * Class XDbCommand.
 * 
 * TDbCommand with SQL logging for development
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class XDbCommand extends TDbCommand
{
	private $params = array();

	public function bindParameter($name, &$value, $dataType = null, $length = null)
	{
		$this->params[$name] = array($value, $dataType);
		parent::bindParameter($name, $value, $dataType, $length);
	}

	public function bindValue($name, $value, $dataType = null)
	{
		$this->params[$name] = array($value, $dataType);
		parent::bindValue($name, $value, $dataType);
	}

	public function getLogStatementText()
	{
		$statement = $this->getPdoStatement();
		$sql = $statement instanceof PDOStatement ?
			$statement->queryString : $this->getText();

		foreach($this->params as $name => $param)
		{
			$value = $param[0];
			$type = $param[1];

			if($value === null)
			{
				$value = 'NULL';
			}
			else
			{
				if($type === null)
				{
					$type = 'text';
					if(is_int($value) || is_long($value))
					{
						$type = 'integer';
					}
					else if(is_float($value) || is_double($value))
					{
						$type = 'float';
					}
					else if(is_bool($value))
					{
						$type = 'boolean';
					}
				}

				switch($type)
				{
					case PDO::PARAM_INT:
					case 'integer':
					case 'float':
						break;

					case PDO::PARAM_STR:
					case 'text':
						$value = "'$value'";
						break;

					case 'boolean':
						$value = $value ? 1 : 0;
						break;

					default:
						throw new TNotSupportedException('Unsupported type "' . $type . '".');
				}
			}

			if(is_int($name))
			{
				$sql = preg_replace('/\?/', $value, $sql, 1);
			}
			else
			{
				$sql = preg_replace("/$name/", $value, $sql);
			}
		}

		return $sql;
	}

	public function execute()
	{
		Prado::log($this->getLogStatementText(), TLogger::DEBUG, 'Application.Data');
		return parent::execute();
	}

	public function query()
	{
		Prado::log($this->getLogStatementText(), TLogger::DEBUG, 'Application.Data');
		return parent::query();
	}

	public function queryRow($fetchAssociative = true)
	{
		Prado::log($this->getLogStatementText(), TLogger::DEBUG, 'Application.Data');
		return parent::queryRow($fetchAssociative);
	}

	public function queryScalar()
	{
		Prado::log($this->getLogStatementText(), TLogger::DEBUG, 'Application.Data');
		return parent::queryScalar();
	}

}
