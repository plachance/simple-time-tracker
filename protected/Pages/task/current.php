<?php

use SimpleTimeTracker\Controllers\ProjectController;
use SimpleTimeTracker\Controllers\TaskController;
use SimpleTimeTracker\Entities\Task;

/**
 * Class current.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 * @property-read TContent $Content
 * @property-read TRepeater $LstProjects
 * @property-read TPanel $PnlTask
 * @property-read TLiteral $LblProject
 * @property-read TLiteral $LblDescription
 * @property-read TDateFormat $LblDateTimeBegin
 * @property-read TDateFormat $LblDateTimeEnd
 * @property-read TButton $BtnStop
 * @property-read TButton $BtnRestart
 * @property-read TButton $BtnStart
 */
class current extends XPage
{
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
	 * @var $em EntityManager
	 */
	public function onInit($param)
	{
		parent::onInit($param);

		if(!$this->getIsPostBack())
		{
			$this->setTitle(Prado::localize('Current task'));
			
			$projectCtrl = new ProjectController();
			$projects = $projectCtrl->getLastProjects($this->getUser()->getId());
			$this->LstProjects->setDataSource($projects);
			$this->LstProjects->dataBind();

			$taskCtrl = new TaskController();
			$task = $taskCtrl->getCurrentTask($this->getUser()->getId());
			if($task != null)
			{
				$this->setTaskId($task->getId());
			}

			$this->showTask($task);
		}
	}

	/**
	 * Initialize controls and show the specified task.
	 * @param Task $task Task to be shown.
	 */
	protected function showTask(Task $task = null)
	{
		$this->PnlTask->setVisible($task != null);
		if($task == null)
		{
			$this->BtnStop->setVisible(false);
			$this->BtnRestart->setVisible(false);
			$this->BtnStart->setVisible(true);
		}
		else
		{
			$this->LblProject->setText($task->getProject()->__toString());
			$this->LblDescription->setText($task->getDescription());
			$this->LblDateTimeBegin->setValue($task->getDateTimeBegin()->getTimestamp());
			$dateTimeEnd = $task->getDateTimeEnd();
			$this->LblDateTimeEnd->setValue($dateTimeEnd == null ? null : $dateTimeEnd->getTimestamp());

			$this->BtnStop->setVisible($dateTimeEnd == null);
			$this->BtnRestart->setVisible($dateTimeEnd != null);
			$this->BtnStart->setVisible($dateTimeEnd != null);
		}
	}

	/**
	 * Stop current task and start a new task with the selected project.
	 * @param TButton $sender
	 * @param TCommandEventParameter $param
	 */
	public function btnProject_OnCommand($sender, $param)
	{
		$projectText = $param->getCommandParameter();
		$ctrl = new TaskController();
		$task = $ctrl->createTask($projectText, null, $this->getUser()->getId());
		$this->setTaskId($task->getId());
		$this->showTask($task);
	}

	/**
	 * Stop current task.
	 * @param TButton $sender
	 * @param TCommandEventParameter $param
	 */
	public function btnStop_OnClick($sender, $param)
	{
		$id = $this->getTaskId();
		if($this->getIsValid() && $id !== null)
		{
			$ctrl = new TaskController();
			$task = $ctrl->stopTask($id);
			$this->showTask($task);
		}
	}

	/**
	 * Restart current task.
	 * @param TButton $sender
	 * @param TCommandEventParameter $param
	 */
	public function btnRestart_OnClick($sender, $param)
	{
		$id = $this->getTaskId();
		if($this->getIsValid() && $id !== null)
		{
			$ctrl = new TaskController();
			$task = $ctrl->restartTask($id);
			$this->setTaskId($task->getId());
			$this->showTask($task);
		}
	}

	/**
	 * Stop current task if needed and redirect user to the new task page.
	 * @param TButton $sender
	 * @param TCommandEventParameter $param
	 */
	public function btnStart_OnClick($sender, $param)
	{
		$id = $this->getTaskId();
		if($this->getIsValid())
		{
			if($id != null)
			{
				$ctrl = new TaskController();
				$ctrl->stopTask($id);
			}
			$this->getResponse()->redirect($this->getService()->constructUrl('task.new'));
		}
	}

}
