<?php

use SimpleTimeTracker\Controllers\TaskController;
use SimpleTimeTracker\DateTime;

/**
 * Class edit.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 * @property-read TContent $Content
 * @property-read TTextBox $TxtProject
 * @property-read TTextBox $TxtDescription
 * @property-read TTextBox $TxtDateTimeBegin
 * @property-read TTextBox $TxtDateTimeEnd
 */
class edit extends XPage
{
	const QS_ID = 'id';

	/**
	 * @return int
	 */
	protected function getTaskId()
	{
		return $this->getControlState('TaskId');
	}

	/**
	 * @param int|null $value
	 */
	protected function setTaskId($value)
	{
		$this->setControlState('TaskId', TPropertyValue::ensureNullIfEmpty($value));
	}

	/**
	 * @param TEventParameter $param
	 */
	public function onInit($param)
	{
		parent::onInit($param);

		if(!$this->getIsPostBack())
		{
			$id = $this->getRequest()->itemAt(self::QS_ID);
			$ctrl = new TaskController();
			$task = $ctrl->getTask($id);
			if($task == null)
			{
				$this->setMessage('Task not found.', MessageType::Error);
				$this->Content->setVisible(false);
			}
			else if($task->getUser()->getName() != $this->getUser()->getName())
			{
				$this->setMessage('You can\'t modify this task.', MessageType::Error);
				$this->Content->setVisible(false);
				Prado::log('Acces denied. Username "' . $this->getUser()->getName() . '", Task "' . $id . '".', TLogger::WARNING, 'Application.pages.Task');
			}
			else
			{
				$this->setTaskId($task->getId());
				$this->TxtProject->setText($task->getProject()->__toString());
				$this->TxtDescription->setText($task->getDescription());
				$this->TxtDateTimeBegin->setText($task->getDateTimeBegin()->format(DateTime::HTML5));
				if($task->getDateTimeEnd())
				{
					$this->TxtDateTimeEnd->setText($task->getDateTimeEnd()->format(DateTime::HTML5));
				}
			}
		}
	}

	/**
	 * Save task.
	 * @param TButton $sender
	 * @param TCommandEventParameter $param
	 */
	public function btnSubmit_OnClick($sender, $param)
	{
		if($this->getIsValid())
		{
			$ctrl = new TaskController();
			$ctrl->modifierTask($this->getTaskId(), $this->TxtProject->getText(), $this->TxtDescription->getText(), new DateTime($this->TxtDateTimeBegin->getText()), $this->TxtDateTimeEnd->getText() == '' ? null : new DateTime($this->TxtDateTimeEnd->getText()));
			$this->setMessage('Task saved.', MessageType::Success);
			$this->getResponse()->redirect($this->getReturnUrl());
		}
	}

}
