<?php

use SimpleTimeTracker\Controllers\SortOrder;

/**
 * Class XDataGrid.
 * 
 * TDataGrid with integrated sort expression and order and disabled integrated 
 * pager. Use the much better XPager instead.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class XDataGrid extends TDataGrid
{
	/**
	 * @return string
	 */
	public function getSortExpression()
	{
		$expr = $this->getViewState('SortExpression');
		if($expr === null)
		{
			return null;
		}
		elseif(($pos = strpos($expr, ',')) !== false)
		{
			$exprs = explode(',', $expr);
			foreach($exprs as &$curExpr)
			{
				$curExpr = $curExpr . ' ' . $this->getSortOrder();
			}
			return implode(',', $exprs);
		}
		
		return $expr . ' ' . $this->getSortOrder();
	}

	/**
	 * @param string $value
	 */
	public function setSortExpression($value)
	{
		$expr = $this->getViewState('SortExpression');
		$this->setViewState('SortExpression', TPropertyValue::ensureNullIfEmpty($value));
		if($expr != $value)
		{
			$this->setSortOrder(null);
		}
	}

	/**
	 * @return SortOrder
	 */
	public function getSortOrder()
	{
		return $this->getViewState('SortOrder');
	}

	/**
	 * @param SortOrder $value
	 */
	public function setSortOrder($value)
	{
		$this->setViewState('SortOrder', $value == null ? null : TPropertyValue::ensureEnum($value, '\SimpleTimeTracker\Controllers\SortOrder'));
	}

	public function toggleSortOrder()
	{
		$order = $this->getSortOrder();
		$this->setSortOrder($order == null || $order == SortOrder::Desc ? SortOrder::Asc : SortOrder::Desc);
	}

	protected function createPager()
	{
		return null;
	}

}

