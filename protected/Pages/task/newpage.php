<?php

use SimpleTimeTracker\Controllers\TaskController;

/**
 * Class newpage.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 * @property-read TContent $Content
 * @property-read TTextBox $TxtProject
 * @property-read TTextBox $TxtDescription
 */
class newpage extends XPage
{
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
			$ctrl->createTask($this->TxtProject->getText(), $this->TxtDescription->getText(), $this->getUser()->getId());
			$this->getResponse()->redirect($this->getReturnUrl());
		}
	}

}
