<?php

/**
 * Class of EchoLogRoute
 * 
 * STDOUT Log Route for Prado TShellApplication.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class EchoLogRoute extends TLogRoute
{
	protected function processLogs($logs)
	{
		if(empty($logs) || !($this->getApplication() instanceof TShellApplication))
		{
			return;
		}

		$response = $this->getApplication()->getResponse();
		foreach($logs as $log)
		{
			$response->write($this->formatLogMessage($log[0], $log[1], $log[2], $log[3]));
		}
	}

}
