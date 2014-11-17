<?php

/**
 * Class SwiftMailer.
 * 
 * SwiftMailer module for Prado.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class SwiftMailer extends TModule
{
	/**
	 * @var Swift_Mailer
	 */
	private $mailer;

	/**
	 * @var mixed[string] Mail headers.
	 */
	private $headers;

	/**
	 * @return Swift_Mailer
	 */
	public function getMailer()
	{
		return $this->mailer;
	}

	/**
	 * @param Swift_Mailer $mailer
	 */
	protected function setMailer(Swift_Mailer $mailer)
	{
		$this->mailer = $mailer;
	}

	/**
	 * @return mixed[string] Mail headers.
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * @param mixed[string] $value Mail headers.
	 */
	public function setHeaders($value)
	{
		$this->headers = TPropertyValue::ensureArray($value);
	}

	public function init($config)
	{
		parent::init($config);

		$this->setMailer(Swift_Mailer::newInstance($this->createTransport($config['transport'])));
	}

	/**
	 * Create a Swift_Transport from the specified configuration.
	 * Configuration format :
	 * array(
	 * 	'class' => 'Transport class name',
	 * 	'properties' => array(
	 * 		'Attribut' => 'Value',
	 * 	),
	 * 	'transports' => array(
	 * 		//List of sub-transports configurations.
	 * 	),
	 * );
	 * @param mixed[string] $config Prado's module configuration
	 * @return Swift_Transport Created transport.
	 */
	protected function createTransport($config)
	{
		switch($config['class'])
		{
			case 'Swift_FailoverTransport':
			case 'Swift_LoadBalancedTransport':
				$config['args'][] = $this->createTransports($config['transports']);
				break;
			case 'Swift_SpoolTransport':
				$config['args'][] = $this->createClassInstance($config['spool']);
				break;
//			case 'Swift_MailTransport':
//			case 'Swift_NullTransport':
//			case 'Swift_SendmailTransport':
//			case 'Swift_SmtpTransport':
//			default:
//				break;
		}

		$transport = $this->createClassInstance($config);

		if(isset($config['plugins']))
		{
			foreach($config['plugins'] as $pluginConfig)
			{
				$plugin = $this->createPlugin($pluginConfig);
				$transport->registerPlugin($plugin);
			}
		}

		return $transport;
	}

	/**
	 * Create plugin instance from specified configuration.
	 * @param mixed[string] $config Prado's module configuration.
	 * @return Swift_Plugins_LoggerPlugin Created plugin.
	 */
	protected function createPlugin($config)
	{
		switch($config['class'])
		{
			case 'Swift_Plugins_LoggerPlugin':
				$config['args'][] = $this->createClassInstance($config['logger']);
				break;
		}

		return $this->createClassInstance($config);
	}

	/**
	 * Create class instance from specified configuration.
	 * @param mixed[string] $config Prado's module configuration.
	 * @return object Class instance from specified configuration.
	 */
	protected function createClassInstance($config)
	{
		$className = $config['class'];
		$arguments = isset($config['args']) ? $config['args'] : array();
		$properties = isset($config['properties']) ? $config['properties'] : array();

		if(($pos = strrpos($className, '.')) !== false)
		{
			Prado::using($className);
			$className = substr($className, $pos + 1);
		}
		$class = new ReflectionClass($className);

		$instance = $class->newInstanceArgs($arguments);

		foreach($properties as $propriete => $valeur)
		{
			$propriete = 'set' . $propriete;
			$instance->$propriete($valeur);
		}

		return $instance;
	}

	/**
	 * Create transports from specified configurations.
	 * @param mixed[int][string] $configs Prado's module configuration.
	 * @return Swift_Transport[] Created transports.
	 */
	protected function createTransports($configs)
	{
		$transports = array();
		foreach($configs as $config)
		{
			$transports[] = $this->createTransport($config);
		}

		return $transports;
	}

	/**
	 * Create a new class instance of one of the message services.
	 *
	 * For example 'mimepart' would create a 'message.mimepart' instance
	 *
	 * @param string $service
	 * @return object|Swift_Message|Swift_MimePart
	 */
	public function createMessage($service = 'message')
	{
		return $this->getMailer()->createMessage($service);
	}

	/**
	 * Send the given Message like it would be sent in a mail client.
	 *
	 * All recipients (with the exception of Bcc) will be able to see the other
	 * recipients this message was sent to.
	 *
	 * Recipient/sender data will be retrieved from the Message object.
	 *
	 * The return value is the number of recipients who were accepted for
	 * delivery.
	 *
	 * @param Swift_Mime_Message $message
	 * @param array              $failedRecipients An array of failures by-reference
	 * @return integer
	 */
	public function send(Swift_Mime_Message $message, &$failedRecipients = null)
	{
		$headers = $this->getHeaders();
		if(!empty($headers))
		{
			foreach($headers as $propriete => $valeur)
			{
				$propriete = 'set' . $propriete;
				$message->$propriete($valeur);
			}
		}

		return $this->getMailer()->send($message, $failedRecipients);
	}

	/**
	 * Register a plugin using a known unique key (e.g. myPlugin).
	 * @param Swift_Events_EventListener $plugin
	 */
	public function registerPlugin(Swift_Events_EventListener $plugin)
	{
		$this->getMailer()->registerPlugin($plugin);
	}

	/**
	 * The Transport used to send messages.
	 * @return Swift_Transport
	 */
	public function getTransport()
	{
		return $this->getMailer()->getTransport();
	}

}