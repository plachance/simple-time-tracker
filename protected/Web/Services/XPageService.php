<?php

/**
 * Class XPageService.
 * 
 * TPageService with added page suffix to allow page name with reserved keywords like "new", "list".
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class XPageService extends TPageService
{
	private $_pageSuffix = 'page';

	public function getPageSuffix()
	{
		return $this->_pageSuffix;
	}

	public function setPageSuffix($value)
	{
		$this->_pageSuffix = $value;
	}

	/**
	 * Creates a page instance based on requested page path.
	 *
	 * If specified page path doesn't exists, we add PageSuffix and retry.
	 * @param string $pagePath the requested page instance
	 * @return TPage if requested page path is invalid
	 * @throws THttpException
	 */
	protected function createPage($pagePath)
	{
		try
		{
			return parent::createPage($pagePath);
		}
		catch(THttpException $ex1)
		{
			try
			{
				return parent::createPage($pagePath . $this->getPageSuffix());
			}
			catch(THttpException $ex2)
			{
				throw $ex1;
			}
		}
	}

}
