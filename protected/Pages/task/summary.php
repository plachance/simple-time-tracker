<?php

use SimpleTimeTracker\Controllers\TaskController;

/**
 * Class summary.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 * @property-read XDataGrid $LstSummary
 */
class summary extends XPage
{
	/**
	 * @var int[] Work years of current user.
	 */
	private $years;

	/**
	 * @param TEventParameter $param
	 */
	public function onInit($param)
	{
		parent::onInit($param);

		$this->initColumns();

		if(!$this->getIsPostBack())
		{
			$this->setTitle(Prado::localize('Summary'));

			$this->initSummary();
		}
	}

	/**
	 * Init summary's columns.
	 */
	protected function initColumns()
	{
		$ctrl = new TaskController();
		$this->years = $ctrl->getYears($this->getUser()->getId());
		$columns = $this->LstSummary->getColumns();
		foreach($this->years as $year)
		{
			$col = new TBoundColumn();
			$col->setHeaderText($year);
			$col->setDataField('year_' . $year);
			$col->setDataFormatString('# SimpleTimeTracker\Util::roundToFraction({0}, 4, 2)');
			$col->setSortExpression('year_' . $year);
			$col->getHeaderStyle()->setCssClass('text-right');
			$col->getItemStyle()->setCssClass('text-right');
			$columns->insertAt($columns->getCount() - 1, $col);
		}
	}

	/**
	 * Init user's summary.
	 */
	protected function initSummary()
	{
		$sortExpr = $this->LstSummary->getSortExpression();
		$pageSize = $this->LstSummary->getPageSize();
		$pageIndex = $this->LstSummary->getCurrentPageIndex();
		$ctrl = new TaskController();
		$summary = $ctrl->getSummary($this->getUser()->getId(), $pageSize, $pageSize * $pageIndex, $itemCount, $sortExpr, $this->years);
		$this->LstSummary->setVirtualItemCount($itemCount);
		$this->LstSummary->setDataSource($summary);
		$this->LstSummary->dataBind();
	}

	/**
	 * @param TPager $sender
	 * @param TPagerPageChangedEventParameter $param
	 */
	public function lstSummary_OnPageIndexChanged($sender, $param)
	{
		$this->LstSummary->setCurrentPageIndex($param->getNewPageIndex());
		$this->initSummary();
	}

	/**
	 * @param TDataGrid $sender
	 * @param TDataGridSortCommandEventParameter $param
	 */
	public function lstSummary_OnSortCommand($sender, $param)
	{
		$this->LstSummary->setSortExpression($param->getSortExpression());
		$this->LstSummary->toggleSortOrder();
		$this->LstSummary->setCurrentPageIndex(0);
		$this->initSummary();
	}

}
