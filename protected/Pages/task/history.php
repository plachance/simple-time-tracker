<?php

use SimpleTimeTracker\Controllers\TaskController;

/**
 * Class history.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 * @property-read TContent $Content
 * @property-read TTextBox $TxtProject
 * @property-read TTextBox $TxtDateTimeBeginFrom
 * @property-read TTextBox $TxtDateTimeBeginTo
 * @property-read XDataGrid $LstTasks
 */
class history extends XPage
{
	/**
	 * @param TEventParameter $param
	 * @var $em EntityManager
	 */
	public function onInit($param)
	{
		parent::onInit($param);

		if(!$this->getIsPostBack())
		{
			$this->setTitle(Prado::localize('History'));
			
			$this->initTasks();
		}
	}

	/**
	 * Init user's tasks list.
	 */
	protected function initTasks($project = null, DateTime $dateTimeBeginFrom = null, DateTime $dateTimeBeginTo = null)
	{
		$sortExpr = $this->LstTasks->getSortExpression();
		$pageSize = $this->LstTasks->getPageSize();
		$pageIndex = $this->LstTasks->getCurrentPageIndex();
		$ctrl = new TaskController();
		$tasks = $ctrl->getTasks($this->getUser()->getId(), $pageSize, $pageSize * $pageIndex, $itemCount, $sortExpr == null ? 't.dateTimeBegin desc' : $sortExpr, $project, $dateTimeBeginFrom, $dateTimeBeginTo);
		$this->LstTasks->setVirtualItemCount($itemCount);
		$this->LstTasks->setDataSource($tasks);
		$this->LstTasks->dataBind();
	}

	/**
	 * @param TButton $sender
	 * @param TEventParameter $param
	 */
	public function btnSubmit_OnClick($sender, $param)
	{
		if($this->getIsValid())
		{
			$dateTimeBeginFrom = $this->TxtDateTimeBeginFrom->getText() == null ? null : new DateTime($this->TxtDateTimeBeginFrom->getText());
			$dateTimeBeginTo = $this->TxtDateTimeBeginTo->getText() == null ? null : new DateTime($this->TxtDateTimeBeginTo->getText());
			$this->initTasks(TPropertyValue::ensureNullIfEmpty($this->TxtProject->getText()), $dateTimeBeginFrom, $dateTimeBeginTo);
		}
	}

	/**
	 * @param TPager $sender
	 * @param TPagerPageChangedEventParameter $param
	 */
	public function lstTasks_OnPageIndexChanged($sender, $param)
	{
		$this->LstTasks->setCurrentPageIndex($param->getNewPageIndex());
		$this->initTasks();
	}

	/**
	 * @param TDataGrid $sender
	 * @param TDataGridSortCommandEventParameter $param
	 */
	public function lstTasks_OnSortCommand($sender, $param)
	{
		$this->LstTasks->setSortExpression($param->getSortExpression());
		$this->LstTasks->toggleSortOrder();
		$this->LstTasks->setCurrentPageIndex(0);
		$this->initTasks();
	}

}
