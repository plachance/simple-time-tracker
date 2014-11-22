<?php

use SimpleTimeTracker\Controllers\TaskController;
use SimpleTimeTracker\DateTime;

/**
 * Class timesheet.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 * @property-read TContent $Content
 * @property-read TTextBox $TxtWeek
 * @property-read TRepeater $LstTasks
 * @property-read TRepeater $LstHoursBeginEnd
 */
class timesheet extends XPage
{
	const DATE_FORMAT = '%a %e %b';

	/**
	 * @return string Selected week. Format "2014-W01".
	 */
	public function getWeek()
	{
		$date = new DateTime();
		$date->add(new DateInterval('P1D')); //Our week begin sunday, not monday.
		return $this->getViewState('Week', $date->toWeekString());
	}

	/**
	 * @param string $value Selected week. Format "2014-W01".
	 */
	protected function setWeek($value)
	{
		$date = new DateTime();
		$date->add(New DateInterval('P1D')); //Our week begin sunday, not monday.
		$this->setViewState('Week', TPropertyValue::ensureString($value), $date->toWeekString());
	}

	/**
	 * @return DateTime Begin date for the selected week.
	 */
	protected function getDateBegin()
	{
		$week = explode('-W', $this->getWeek());
		$date = new DateTime();
		$date->setISODate($week[0], $week[1]);
		$date->sub(new DateInterval('P1D')); //Our week begin sunday, not monday.
		return $date;
	}

	/**
	 * @param int $dayOfWeek Day of week. 0 = sunday and 6 = saturday.
	 * @return string Local formated date string for the specified day of week.
	 */
	protected function getDateDayWeek($dayOfWeek)
	{
		$date = $this->getDateBegin();
		$date->add(new DateInterval('P' . $dayOfWeek . 'D'));
		return strftime(self::DATE_FORMAT, $date->getTimestamp());
	}

	public function onInit($param)
	{
		parent::onInit($param);

		if(!$this->getIsPostBack())
		{
			$this->setTitle(Prado::localize('Timesheet'));
			
			$date = new DateTime();
			$date->add(New DateInterval('P1D')); //Our week begin sunday, not monday.
			$semaine = $date->toWeekString();
			$this->setWeek($semaine);
			$this->TxtWeek->setText($semaine);

			$this->showTasks();
		}
	}

	protected function showTasks()
	{
		$dateBegin = $this->getDateBegin();
		$ctrl = new TaskController();
		$tasks = $ctrl->getTimeSheet($this->getUser()->getId(), $dateBegin);
		$this->LstTasks->setDataSource($tasks);
		$this->LstTasks->dataBind();

		if(empty($tasks))
		{
			$this->LstHoursBeginEnd->reset();
		}
		else
		{
			$hours = $ctrl->getTimePeriods($this->getUser()->getId(), $dateBegin);
			$this->LstHoursBeginEnd->setDataSource($hours);
			$this->LstHoursBeginEnd->dataBind();
		}
	}

	/**
	 * @param TButton $sender
	 * @param TEventParameter $param
	 */
	public function btnPrevious_OnClick($sender, $param)
	{
		if($this->getIsValid())
		{
			$date = $this->getDateBegin();
			$week = $date->toWeekString();
			$this->setWeek($week);
			$this->TxtWeek->setText($week);
			$this->showTasks();
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
			$this->setWeek($this->TxtWeek->getText());
			$this->showTasks();
		}
	}

	/**
	 * @param TButton $sender
	 * @param TEventParameter $param
	 */
	public function btnNext_OnClick($sender, $param)
	{
		if($this->getIsValid())
		{
			$date = $this->getDateBegin();
			$date->add(new DateInterval('P8D'));
			$week = $date->toWeekString();
			$this->setWeek($week);
			$this->TxtWeek->setText($week);
			$this->showTasks();
		}
	}

}
