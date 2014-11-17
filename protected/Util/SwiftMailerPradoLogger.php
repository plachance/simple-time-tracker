<?php

/**
 * Class SwiftMailerPradoLogger.
 * 
 * SwiftMailer logger through Prado.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class SwiftMailerPradoLogger implements Swift_Plugins_Logger
{
	private $level;
	private $category;

	public function getLevel()
	{
		return $this->level;
	}

	public function setLevel($level)
	{
		$this->level = $level;
	}

	public function getCategory()
	{
		return $this->category;
	}

	public function setCategory($category)
	{
		$this->category = $category;
	}

	public function __construct($level = TLogger::INFO, $category = 'Application.Util.SwiftMailer')
	{
		$this->setLevel($level);
		$this->setCategory($category);
	}

	public function add($entry)
	{
		Prado::log($entry, $this->getLevel(), $this->getCategory());
	}

	public function clear()
	{
		
	}

	public function dump()
	{
		
	}

}