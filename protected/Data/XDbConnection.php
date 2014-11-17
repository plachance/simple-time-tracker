<?php

Prado::using('Application.Data.XDbCommand');

/**
 * Class XDbConnection.
 * 
 * TDbConnection with SQL logging for development.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class XDbConnection extends TDbConnection
{
	public function __construct($dsn = '', $username = '', $password = '', $charset = '')
	{
		parent::__construct($dsn, $username, $password, $charset);
		$this->setTransactionClass('Application.Data.XDbTransaction');
	}

	public function createCommand($sql)
	{
		if($this->getActive())
		{
			return new XDbCommand($this, $sql);
		}
		else
		{
			throw new TDbException('dbconnection_connection_inactive');
		}
	}

	public function beginTransaction()
	{
		$trx = parent::beginTransaction();
		Prado::log('START TRANSACTION', TLogger::DEBUG, 'Application.Data');
		return $trx;
	}

}
