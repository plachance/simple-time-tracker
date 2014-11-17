<?php

/**
 * Class XButton.
 *
 * HTML5 Bouton.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class XButton extends TButton
{
	protected function getTagName()
	{
		return 'button';
	}

	/**
	 * @param THtmlWriter $writer
	 */
	protected function addAttributesToRender($writer)
	{
		parent::addAttributesToRender($writer);

		if($this->getText() == '')
		{
			$writer->removeAttribute('value');
		}
	}

	public function renderContents($writer)
	{
		TWebControl::renderContents($writer);
	}

}
