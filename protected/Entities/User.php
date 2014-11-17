<?php

namespace SimpleTimeTracker\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 */
class User implements \IUser
{
	/**
	 * @var string
	 */
	private $username;

	/**
	 * @var string
	 */
	private $usernameCanonical;

	/**
	 * @var string
	 */
	private $email;

	/**
	 * @var string
	 */
	private $emailCanonical;

	/**
	 * @var \DateTime
	 */
	private $dateTimeCreated;

	/**
	 * @var \DateTime
	 */
	private $dateTimeLastLogin;

	/**
	 * @var boolean
	 */
	private $enabled;

	/**
	 * @var boolean
	 */
	private $isConfirmed;

	/**
	 * @var string
	 */
	private $confirmationCode;

	/**
	 * @var \DateTime
	 */
	private $dateTimeConfirmationTimeout;

	/**
	 * @var string
	 */
	private $password;

	/**
	 * @var string
	 */
	private $hashSalt;

	/**
	 * @var array
	 */
	private $roles;

	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $tasks;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * Set username
	 *
	 * @param string $username
	 * @return User
	 */
	public function setUsername($username)
	{
		$this->username = $username;

		return $this;
	}

	/**
	 * Get username
	 *
	 * @return string 
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * Set usernameCanonical
	 *
	 * @param string $usernameCanonical
	 * @return User
	 */
	public function setUsernameCanonical($usernameCanonical)
	{
		$this->usernameCanonical = $usernameCanonical;

		return $this;
	}

	/**
	 * Get usernameCanonical
	 *
	 * @return string 
	 */
	public function getUsernameCanonical()
	{
		return $this->usernameCanonical;
	}

	/**
	 * Set email
	 *
	 * @param string $email
	 * @return User
	 */
	public function setEmail($email)
	{
		$this->email = $email;

		return $this;
	}

	/**
	 * Get email
	 *
	 * @return string 
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Set emailCanonical
	 *
	 * @param string $emailCanonical
	 * @return User
	 */
	public function setEmailCanonical($emailCanonical)
	{
		$this->emailCanonical = $emailCanonical;

		return $this;
	}

	/**
	 * Get emailCanonical
	 *
	 * @return string 
	 */
	public function getEmailCanonical()
	{
		return $this->emailCanonical;
	}

	/**
	 * Set dateTimeCreated
	 *
	 * @param \DateTime $dateTimeCreated
	 * @return User
	 */
	public function setDateTimeCreated($dateTimeCreated)
	{
		$this->dateTimeCreated = $dateTimeCreated;

		return $this;
	}

	/**
	 * Get dateTimeCreated
	 *
	 * @return \DateTime 
	 */
	public function getDateTimeCreated()
	{
		return $this->dateTimeCreated;
	}

	/**
	 * Set dateTimeLastLogin
	 *
	 * @param \DateTime $dateTimeLastLogin
	 * @return User
	 */
	public function setDateTimeLastLogin($dateTimeLastLogin)
	{
		$this->dateTimeLastLogin = $dateTimeLastLogin;

		return $this;
	}

	/**
	 * Get dateTimeLastLogin
	 *
	 * @return \DateTime 
	 */
	public function getDateTimeLastLogin()
	{
		return $this->dateTimeLastLogin;
	}

	/**
	 * Set enabled
	 *
	 * @param boolean $enabled
	 * @return User
	 */
	public function setEnabled($enabled)
	{
		$this->enabled = $enabled;

		return $this;
	}

	/**
	 * Get enabled
	 *
	 * @return boolean 
	 */
	public function getEnabled()
	{
		return $this->enabled;
	}

	/**
	 * Set isConfirmed
	 *
	 * @param boolean $isConfirmed
	 * @return User
	 */
	public function setIsConfirmed($isConfirmed)
	{
		$this->isConfirmed = $isConfirmed;

		return $this;
	}

	/**
	 * Get isConfirmed
	 *
	 * @return boolean 
	 */
	public function getIsConfirmed()
	{
		return $this->isConfirmed;
	}

	/**
	 * Set confirmationCode
	 *
	 * @param string $confirmationCode
	 * @return User
	 */
	public function setConfirmationCode($confirmationCode)
	{
		$this->confirmationCode = $confirmationCode;

		return $this;
	}

	/**
	 * Get confirmationCode
	 *
	 * @return string 
	 */
	public function getConfirmationCode()
	{
		return $this->confirmationCode;
	}

	/**
	 * Set dateTimeConfirmationTimeout
	 *
	 * @param \DateTime $dateTimeConfirmationTimeout
	 * @return User
	 */
	public function setDateTimeConfirmationTimeout($dateTimeConfirmationTimeout)
	{
		$this->dateTimeConfirmationTimeout = $dateTimeConfirmationTimeout;

		return $this;
	}

	/**
	 * Get dateTimeConfirmationTimeout
	 *
	 * @return \DateTime 
	 */
	public function getDateTimeConfirmationTimeout()
	{
		return $this->dateTimeConfirmationTimeout;
	}

	/**
	 * Set password
	 *
	 * @param string $password
	 * @return User
	 */
	public function setPassword($password)
	{
		$this->password = $password;

		return $this;
	}

	/**
	 * Get password
	 *
	 * @return string 
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * Set hashSalt
	 *
	 * @param string $hashSalt
	 * @return User
	 */
	public function setHashSalt($hashSalt)
	{
		$this->hashSalt = $hashSalt;

		return $this;
	}

	/**
	 * Get hashSalt
	 *
	 * @return string 
	 */
	public function getHashSalt()
	{
		return $this->hashSalt;
	}

	/**
	 * Set roles
	 *
	 * @param array $roles
	 * @return User
	 */
	public function setRoles($roles)
	{
		$this->roles = $roles;

		return $this;
	}

	/**
	 * Get roles
	 *
	 * @return array 
	 */
	public function getRoles()
	{
		return $this->roles;
	}

	/**
	 * Get id
	 *
	 * @return integer 
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Add tasks
	 *
	 * @param \SimpleTimeTracker\Entities\Task $tasks
	 * @return User
	 */
	public function addTask(\SimpleTimeTracker\Entities\Task $tasks)
	{
		$this->tasks[] = $tasks;

		return $this;
	}

	/**
	 * Remove tasks
	 *
	 * @param \SimpleTimeTracker\Entities\Task $tasks
	 */
	public function removeTask(\SimpleTimeTracker\Entities\Task $tasks)
	{
		$this->tasks->removeElement($tasks);
	}

	/**
	 * Get tasks
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getTasks()
	{
		return $this->tasks;
	}

	//<editor-fold desc="IUser">
	private $isGuest = false;

	public function getIsGuest()
	{
		return $this->isGuest;
	}

	public function setIsGuest($value)
	{
		$this->isGuest = (boolean) $value;
	}

	public function getName()
	{
		return $this->getUsername();
	}

	public function setName($value)
	{
		$this->setUsername($value);
	}

	public function isInRole($role)
	{
		foreach ($this->getRoles() as $r)
			if (strcasecmp($role, $r) === 0)
				return true;
		return false;
	}

	public function loadFromString($string)
	{
		$user = unserialize($string);
		return $user === false ? $this : $user;
	}

	public function saveToString()
	{
		return serialize($this);
	}

	public function __sleep()
	{
		return array(
			'username',
			'usernameCanonical',
			'email',
			'emailCanonical',
			'dateTimeCreated',
			'dateTimeLastLogin',
			'enabled',
			'isConfirmed',
			'confirmationCode',
			'dateTimeConfirmationTimeout',
			'password',
			'hashSalt',
			'roles',
			'id',
			'isGuest',
		);
	}
	//</editor-fold>
}
