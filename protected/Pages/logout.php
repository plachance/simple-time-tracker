<?php

/**
 * Class logout.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class logout extends TPage
{
	public function onInit($param)
	{
		parent::onInit($param);

		$auth = $this->getApplication()->getModule('auth');
		/* @var $auth TAuthManager */
		$auth->logout();
		$this->getResponse()->redirect($this->getService()->constructUrl(''));
	}

}

