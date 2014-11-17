<?php

use SimpleTimeTracker\Controllers\UserController;

/**
 * Class recover.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 * @property-read TPanel $PnlEmail
 * @property-read TTextBox $TxtUsername
 * @property-read TPanel $PnlRecover
 * @property-read TTextBox $TxtPassword
 * @property-read TTextBox $TxtPasswordConfirm
 */
class recover extends XPage
{
	const QS_USERNAME = 'u';
	const QS_CONFIRMATION_CODE = 'code';

	protected function getUsername()
	{
		return $this->getControlState('Username');
	}

	protected function setUsername($value)
	{
		$this->setControlState('Username', TPropertyValue::ensureNullIfEmpty($value));
	}

	public function onInit($param)
	{
		parent::onInit($param);

		if(!$this->getIsPostBack())
		{
			$username = $this->getRequest()->itemAt(self::QS_USERNAME);
			if($username == null)
			{
				$this->PnlEmail->setVisible(true);
			}
			else
			{
				$codeConfirm = $this->getRequest()->itemAt(self::QS_CONFIRMATION_CODE);
				$users = new UserController();
				if($users->getIsConfirmationCodeValid($username, $codeConfirm))
				{
					$this->setUsername($username);
					$this->PnlRecover->setVisible(true);
				}
				else
				{
					$this->setMessage('Confirmation code invalid or expired.', MessageType::Error);
				}
			}
		}
	}

	/**
	 * @param TButton $sender
	 * @param TEventParameter $param
	 */
	public function btnSend_OnClick($sender, $param)
	{
		if($this->getIsValid())
		{
			$users = new UserController();
			try
			{
				$users->sendRecoverMail($this->TxtUsername->getText());
				$this->setMessage('An email containing password recovery instruction has been sent.', MessageType::Success);
				$this->PnlEmail->setVisible(false);
			}
			catch(TInvalidDataValueException $ex)
			{
				$this->setMessage($ex->getMessage(), MessageType::Error);
			}
			catch(Exception $ex)
			{
				$this->setMessage('Unknown error encountered. Please try again later.', MessageType::Error);
				Prado::log($ex, TLogger::ERROR, 'Application.Pages.Recover');
			}
		}
	}

	/**
	 * @param TButton $sender
	 * @param TEventParameter $param
	 */
	public function btnModify_OnClick($sender, $param)
	{
		if($this->getIsValid())
		{
			$users = new UserController();
			$users->changePassword($this->getUsername(), $this->TxtPassword->getText());
			$this->setMessage('New password saved. You can now sign in.', MessageType::Success);
			$this->PnlRecover->setVisible(false);
		}
	}

}
