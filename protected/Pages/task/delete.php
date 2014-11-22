<?php

use SimpleTimeTracker\Controllers\TaskController;

/**
 * Class delete.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 * @property-read TContent $Content
 */
class delete extends XPage
{
	/**
	 * QueryString for task id.
	 */
	const QS_ID = 'id';

	/**
	 * @return int
	 */
	public function getTaskId()
	{
		return $this->getViewState('TaskId');
	}

	/**
	 * @param int $value
	 */
	protected function setTaskId($value)
	{
		$this->setViewState('TaskId', $value);
	}

	/**
	 * @param TEventParameter $param
	 */
	public function onInit($param)
	{
		parent::onInit($param);

		if(!$this->getIsPostBack())
		{
			$this->setTitle(Prado::localize('Delete task'));

			$taskId = $this->getRequest()->itemAt(self::QS_ID);
			$ctrl = new TaskController();
			try
			{
				if($ctrl->getIsUserTask($taskId, $this->getUser()->getId()))
				{
					$this->setTaskId($taskId);
				}
				else
				{
					$this->setMessage(Prado::localize('You can\'t delete this task.'), MessageType::Error);
					$this->Content->setVisible(false);
				}
			}
			catch(TInvalidDataValueException $ex)
			{
				$this->setMessage($ex->getMessage(), MessageType::Error);
				$this->Content->setVisible(false);
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
			$ctrl = new TaskController();
			$ctrl->deleteTask($this->getTaskId());
			$this->setMessage(Prado::localize('Task deleted.'), MessageType::Success);
			$this->getResponse()->redirect($this->getReturnUrl());
		}
	}

}
