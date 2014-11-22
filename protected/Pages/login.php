<?php

/**
 * Class login.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 * @property-read TTextBox $TxtUsername
 * @property-read TTextBox $TxtPassword
 * @property-read TCheckBox $ChkStaySignedIn
 */
class login extends XPage
{
	const EXPIRE_DEFAULT = 604800; //1 week

	/**
	 * @param TEventParameter $param
	 */
	public function onInit($param)
	{
		parent::onInit($param);

		if(!$this->getIsPostBack())
		{
			$this->setTitle(Prado::localize('Sign in'));
			
			if(!$this->getUser()->getIsGuest())
			{
				$this->getResponse()->redirect($this->getService()->constructUrl(''));
			}
		}
	}

	/**
	 * @param TButton $sender
	 * @param TEventParameter $param
	 */
	public function btnSubmit_OnClick($sender, $param)
	{
		if($this->getIsValid())
		{
			$auth = $this->getApplication()->getModule('auth');
			/* @var $auth TAuthManager */
			if($auth->login($this->TxtUsername->getText(), $this->TxtPassword->getText(), $this->ChkStaySignedIn->getChecked() ? self::EXPIRE_DEFAULT : 0))
			{
				$returnUrl = $auth->getReturnUrl();
				if($returnUrl == null || stripos($returnUrl, 'login') !== false)
				{
					$returnUrl = $this->getService()->constructUrl('');
				}

				$this->getResponse()->redirect($returnUrl);
			}
			else
			{
				$this->setMessage(Prado::localize('Username of password invalid.'), MessageType::Error);
			}
		}
	}

}
