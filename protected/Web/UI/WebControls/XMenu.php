<?php

/**
 * Class XMenu.
 * 
 * Special TBulletedList for application menu.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class XMenu extends TBulletedList
{
	/**
	 * @var boolean cached property value of Enabled
	 */
	private $_isEnabled;

	public function onInit($param)
	{
		parent::onInit($param);

		if(!$this->getPage()->getIsPostBack())
		{
			$this->setDisplayMode(TBulletedListDisplayMode::HyperLink);
			$this->setDataTextFormatString('#$this->Service->constructUrl({0})');
			$this->setCssClass('nav nav-tabs ' . $this->getCssClass());
		}
	}

	/**
	 * Adds attribute name-value pairs to renderer.
	 * This overrides the parent implementation with additional bulleted list specific attributes.
	 * @param THtmlWriter the writer used for the rendering purpose
	 */
	protected function addAttributesToRender($writer)
	{
		$needStart = false;
		switch($this->getBulletStyle())
		{
			case TBulletStyle::None:
				$writer->addStyleAttribute('list-style-type', 'none');
				$needStart = true;
				break;
			case TBulletStyle::Numbered:
				$writer->addStyleAttribute('list-style-type', 'decimal');
				$needStart = true;
				break;
			case TBulletStyle::LowerAlpha:
				$writer->addStyleAttribute('list-style-type', 'lower-alpha');
				$needStart = true;
				break;
			case TBulletStyle::UpperAlpha:
				$writer->addStyleAttribute('list-style-type', 'upper-alpha');
				$needStart = true;
				break;
			case TBulletStyle::LowerRoman:
				$writer->addStyleAttribute('list-style-type', 'lower-roman');
				$needStart = true;
				break;
			case TBulletStyle::UpperRoman:
				$writer->addStyleAttribute('list-style-type', 'upper-roman');
				$needStart = true;
				break;
			case TBulletStyle::Disc:
				$writer->addStyleAttribute('list-style-type', 'disc');
				break;
			case TBulletStyle::Circle:
				$writer->addStyleAttribute('list-style-type', 'circle');
				break;
			case TBulletStyle::Square:
				$writer->addStyleAttribute('list-style-type', 'square');
				break;
			case TBulletStyle::CustomImage:
				$url = $this->getBulletImageUrl();
				$writer->addStyleAttribute('list-style-image', "url($url)");
				break;
		}

		if($needStart && ($start = $this->getFirstBulletNumber()) != 1)
		{
			$writer->addAttribute('start', "$start");
		}

		TWebControl::addAttributesToRender($writer);
	}

	/**
	 * Renders the body contents.
	 * @param THtmlWriter the writer for the rendering purpose.
	 */
	public function renderContents($writer)
	{
		$defaultPage = $this->getApplication()->getService()->getDefaultPage();
		$pagePath = $this->getPage()->getPagePath();
		$this->_isEnabled = $this->getEnabled(true);
		$writer->writeLine();
		foreach($this->getItems() as $index => $item)
		{
			if($item->getHasAttributes())
			{
				$writer->addAttributes($item->getAttributes());
			}
			if($item->getValue() == $pagePath || $item->getValue() == '' && $pagePath == $defaultPage)
			{
				$writer->addAttribute('class', 'active');
			}
			$writer->renderBeginTag('li');
			$this->renderBulletText($writer, $item, $index);
			$writer->renderEndTag();
			$writer->writeLine();
		}
	}

	protected function renderHyperLinkItem($writer, $item, $index)
	{
		if(!$this->_isEnabled || !$item->getEnabled())
		{
			$writer->addAttribute('disabled', 'disabled');
		}
		else
		{
			$writer->addAttribute('href', $this->formatDataValue($this->getDataTextFormatString(), $item->getValue()));
			if(($target = $this->getTarget()) !== '')
			{
				$writer->addAttribute('target', $target);
			}
		}
		if(($accesskey = $this->getAccessKey()) !== '')
		{
			$writer->addAttribute('accesskey', $accesskey);
		}
		$writer->renderBeginTag('a');
		$writer->write(THttpUtility::htmlEncode($item->getText()));
		$writer->renderEndTag();
	}

}
