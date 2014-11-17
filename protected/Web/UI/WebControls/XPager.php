<?php

/**
 * Class XPager.
 * 
 * Replace TPager's numeric mode with one similar to most pagers on the Internet. 
 *
 * In numeric mode, buttons First, Previous, Next and Last are shown surrounding
 * page numbers buttons.
 * First : Go to first page.
 * Previous : Go to previous page. If the user is on page 3, it will go to page 2.
 * Next : Go to next page. If user is on page 3, it will go to page 4.
 * Last : Go to last page.
 * Liste des # de pages : A maximum of PageButtonCount are shown. There's always 
 * PageButtonCount generated to avoir problems when pager's structure change on 
 * callback. Unused buttons are hidden. The current page is, if possible, shown
 * in the middle of the pager. When previous/next page is shown, page numbers 
 * buttons are shifted (if current page is in the middle).
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class XPager extends TPager
{
	protected $_tagName = 'div';

	/**
	 * Returns the tag name used for this control.
	 * By default, the tag name is 'div'.
	 * You can override this method to provide customized tag names.
	 * @return string tag name of the control to be rendered
	 */
	public function getTagName()
	{
		return $this->_tagName;
	}

	public function setTagName($value)
	{
		$this->_tagName = TPropertyValue::ensureString($value);
	}

	/**
	 * Builds a numeric pager
	 */
	protected function buildNumericPager()
	{
		$buttonType = $this->getButtonType();
		$controls = $this->getControls();
		$pageCount = $this->getPageCount();
		$pageIndex = $this->getCurrentPageIndex() + 1;
		$maxButtonCount = $this->getPageButtonCount() % 2 == 0 ? $this->getPageButtonCount() - 1 : $this->getPageButtonCount(); //On affiche tjr un maximum de bouton impair afin que le CurrentPageIndex soit affiché au centre.
		$buttonCount = $maxButtonCount > $pageCount ? $pageCount : $maxButtonCount;
		$startPageIndex = 1;
		$endPageIndex = $buttonCount;

		$controls->add('<ul class="pagination">');

		//First button
		$button = $this->createPagerButton($buttonType, $pageIndex > 1, $this->getFirstPageText(), self::CMD_PAGE_FIRST, '');
		$controls->add($pageIndex > 1 ? '<li>' : '<li class="disabled">');
		$controls->add($button);
		$controls->add('</li>');
		$controls->add("\n");

		//Previous button
		$button = $this->createPagerButton($buttonType, $pageIndex > 1, $this->getPrevPageText(), self::CMD_PAGE_PREV, '');
		$controls->add($pageIndex > 1 ? '<li>' : '<li class="disabled">');
		$controls->add($button);
		$controls->add('</li>');
		$controls->add("\n");

		//Number buttons
		$startPageIndex = $pageIndex - floor(($buttonCount - 1) / 2);
		if($startPageIndex < 1)
		{
			$startPageIndex = 1;
		}

		$endPageIndex = $startPageIndex + $buttonCount - 1;
		if($endPageIndex > $pageCount)
		{
			$endPageIndex = $pageCount;
			$startPageIndex = $endPageIndex - $buttonCount + 1;
		}

		$lastIndex = $startPageIndex + $maxButtonCount - 1;
		for($i = $startPageIndex; $i <= $endPageIndex; $i++)
		{
			$button = $this->createPagerButton($buttonType, $i != $pageIndex && $i <= $endPageIndex, "$i", self::CMD_PAGE, "$i");
			$controls->add($i == $pageIndex ? '<li class="active">' : '<li>');
			$controls->add($button);
			$controls->add('</li>');
			$controls->add("\n");
		}

		//Next button
		$button = $this->createPagerButton($buttonType, $pageIndex < $pageCount, $this->getNextPageText(), self::CMD_PAGE_NEXT, '');
		$controls->add($pageIndex < $pageCount ? '<li>' : '<li class="disabled">');
		$controls->add($button);
		$controls->add('</li>');
		$controls->add("\n");

		//Last button
		$button = $this->createPagerButton($buttonType, $pageIndex < $pageCount, $this->getLastPageText(), self::CMD_PAGE_LAST, '');
		$controls->add($pageIndex < $pageCount ? '<li>' : '<li class="disabled">');
		$controls->add($button);
		$controls->add('</li>');

		$controls->add('</ul>');
	}

}
