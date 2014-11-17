<?php

/**
 * Class XPage.
 * 
 * TPage with additional functionality:
 * -Return page path with query string "r".
 * -Application wide messages that can be shown even after a redirection.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class XPage extends TPage
{
	/**
	 * QueryString return page path.
	 */
	const QS_RETURN_PAGE_PATH = 'r';

	/**
	 * @return string|null Return PagePath.
	 */
	public function getReturnPagePath()
	{
		return $this->getRequest()->itemAt(self::QS_RETURN_PAGE_PATH);
	}

	/**
	 * @return string Return URL.
	 */
	public function getReturnUrl()
	{
		return $this->getService()->constructUrl($this->getReturnPagePath());
	}

	/**
	 * Add the specified message to the application messages list. The message 
	 * will be shown inside a colored rectancle based on message type.
	 * @param string $message Message to add..
	 * @param string $type MessageType Type of the message. 
	 * @param bool $repeat True if the message can be shown multiple times.
	 */
	public function setMessage($message, $type = MessageType::Info, $repeat = true)
	{
		if(!$message)
		{
			return;
		}

		$type = TPropertyValue::ensureEnum($type == null ? MessageType::Info : $type, 'MessageType');
		$session = $this->getSession();
		$session->open();
		$msgs = $session->itemAt('messages');
		if($msgs == null)
		{
			$msgs = array();
		}

		if(!isset($msgs[$type]) || $repeat || !in_array($message, $msgs[$type]))
		{
			$msgs[$type][] = $message;
		}

		$session->add('messages', $msgs);
	}

	/**
	 * Affiche les messages de l'application dans le panel des messages.
	 */
	protected function renderMessages()
	{
		$session = $this->getSession();
		$session->open();
		$messages = $session->itemAt('messages');
		if(!empty($messages))
		{
			$types = array(
				MessageType::Error => array('alert-danger', 'glyphicon-remove-sign'),
				MessageType::Warning => array('alert-warning', 'glyphicon-warning-sign'),
				MessageType::Info => array('alert-info', 'glyphicon-info-sign'),
				MessageType::Success => array('alert-success', 'glyphicon-ok-sign'),
			);

			foreach($types as $type => $infos)
			{
				if(isset($messages[$type]) && !empty($messages[$type]))
				{
					$contenu = '<div class="alert ' . $infos[0] . ' alert-dismissable">
						<span class="glyphicon ' . $infos[1] . '"></span>
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<ul style="display:inline-block">';
					foreach($messages[$type] as $msg)
					{
						$contenu .= '<li>' . $msg . '</li>';
					}
					$contenu .= '</ul>';
				}
			}
			$this->getMaster()->PnlMessages->getControls()->add($contenu);

			$session->remove('messages');
		}
	}

	/**
	 * @param TEventParameter $param
	 */
	public function onPreRender($param)
	{
		parent::onPreRender($param);

		$this->renderMessages();
	}

}
