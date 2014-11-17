<?php

/**
 * Class XDbTransaction.
 * 
 * TDbTransaction with SQL logging for development.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class XDbTransaction extends TDbTransaction
{
	/**
	 * Commits a transaction.
	 * @throws TDbException if the transaction or the DB connection is not active.
	 */
	public function commit()
	{
		parent::commit();
		Prado::log('COMMIT', TLogger::DEBUG, 'Application.Data');
	}

	/**
	 * Rolls back a transaction.
	 * @throws TDbException if the transaction or the DB connection is not active.
	 */
	public function rollback()
	{
		parent::rollback();
		Prado::log('ROLLBACK', TLogger::DEBUG, 'Application.Data');
	}

}
