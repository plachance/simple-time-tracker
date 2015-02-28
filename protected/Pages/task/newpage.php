<?php

use SimpleTimeTracker\Controllers\TaskController;

/**
 * Class newpage.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 * @property-read TContent $Content
 * @property-read TTextBox $TxtProject
 * @property-read TTextBox $TxtDescription
 * @property-read TTextBox $TxtDateTimeBegin
 * @property-read TTextBox $TxtDateTimeEnd
 */
class newpage extends XPage
{
	public function onInit($param)
	{
		parent::onInit($param);

		if(!$this->getIsPostBack())
		{
			$this->setTitle(Prado::localize('Create task'));
		}
	}

	/**
	 * Create a new task.
	 * @param TButton $sender
	 * @param TCommandEventParameter $param
	 */
	public function btnSubmit_OnClick($sender, $param)
	{
		if($this->getIsValid())
		{
			$ctrl = new TaskController();
			$ctrl->createTask($this->TxtProject->getText(),
				$this->TxtDescription->getText(),
				$this->TxtDateTimeBegin->getText() == '' ? null : new DateTime($this->TxtDateTimeBegin->getText()),
				$this->TxtDateTimeEnd->getText() == '' ? null : new DateTime($this->TxtDateTimeEnd->getText()),
				$this->getUser()->getId());
			$this->getResponse()->redirect($this->getReturnUrl());
		}
	}

}
