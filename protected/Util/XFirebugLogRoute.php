<?php

/**
 * Class XFirebugLogRoute.
 * 
 * Allow logging of strings containing linebreak through the browser console logger.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class XFirebugLogRoute extends TFirebugLogRoute
{
	public function processLogs($logs)
	{
		if(!($this->getApplication() instanceof TShellApplication))
		{
			parent::processLogs($logs);
		}
	}

	protected function renderMessage($log, $info)
	{
		$logfunc = $this->getFirebugLoggingFunction($log[1]);
		$total = sprintf('%0.6f', $info['total']);
		$delta = sprintf('%0.6f', $info['delta']);
		$msg = preg_replace('/(?<!\\\)\\\n/', "\n", $log[0]); //Repleace "\n" with real linebreaks so they are not escaped with addslashes.
		$msg = trim($this->formatLogMessage($msg, $log[1], $log[2], ''));
		$msg = preg_replace('/\(line[^\)]+\)$/', '', $msg); //remove line number info
		$msg = "[{$total}] [{$delta}] " . $msg; // Add time spent and cumulated time spent
		$msg = addslashes($msg);
		$msg = preg_replace('/\r\n|\n\r|\n/', '\n', $msg); //Replace line breaks to avoid Javascript errors.
		$string = $logfunc . '(\'' . $msg . '\');' . "\n";

		return $string;
	}

}
