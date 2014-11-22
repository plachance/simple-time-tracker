<?php

/**
 * Class XHTTPResponse.
 * 
 * THttpResponse that plays nice with CLI applications. Disabled output buffering 
 * and no headers sent when application is instance of TShellApplication.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class XHttpResponse extends THttpResponse
{
	public function setBufferOutput($value)
	{
		if($this->getApplication() instanceof TShellApplication)
		{
			$value = TPropertyValue::ensureBoolean($value);
			if($value)
			{
				throw new TInvalidOperationException('httpresponse_bufferoutput_unchangeable');
			}
			else
			{
				parent::setBufferOutput($value);
			}
		}
		else
		{
			parent::setBufferOutput($value);
		}
	}

	public function __construct()
	{
		parent::__construct();

		if($this->getApplication() instanceof TShellApplication)
		{
			$this->setBufferOutput(false);
		}
	}

	public function ensureHeadersSent()
	{
		if(!($this->getApplication() instanceof TShellApplication))
		{
			$this->ensureHttpHeaderSent();
			$this->ensureContentTypeHeaderSent();
		}
	}

}
