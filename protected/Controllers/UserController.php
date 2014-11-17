<?php

namespace SimpleTimeTracker\Controllers;

use DateInterval;
use Doctrine\ORM\EntityRepository;
use Exception;
use Prado;
use SimpleTimeTracker\DateTime;
use SimpleTimeTracker\Entities\User;
use Swift_Message;
use Swift_Plugins_DecoratorPlugin;
use SwiftMailer;
use TInvalidDataValueException;

/**
 * Class UserController
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class UserController extends Controller
{
	const GUEST_USERNAME = 'Guest';

	/**
	 * @var string Password hash algorithm supported by the hash() function. Default to 'sha512'.
	 */
	private $passwordHashAlgorithm = 'sha512';

	/**
	 * @return string Password hash algorithm supported by the hash() function. Default to 'sha512'.
	 */
	public function getPasswordHashAlgorithm()
	{
		return $this->passwordHashAlgorithm;
	}

	/**
	 * @param string $algorithm Password hash algorithm supported by the hash() function.
	 */
	public function setPasswordHashAlgorithm($algorithm)
	{
		if($algorithm == null)
		{
			throw new TInvalidDataValueException('Algorithm must not be null.');
		}

		$this->passwordHashAlgorithm = $algorithm;
	}

	/**
	 * @return EntityRepository
	 */
	protected function getRepository()
	{
		return $this->getEntityManager()->getRepository('SimpleTimeTracker\Entities\User');
	}

	public function canonicalizeUsername($username)
	{
		return strtolower($username);
	}

	public function canonicalizeEmail($email)
	{
		return strtolower($email);
	}

	/**
	 * @param string $username
	 * @return User|null Specified user or null if username does not exists.
	 */
	public function getUser($username = null)
	{
		if($username === null)
		{
			$user = new User();
			$user->setIsGuest(true);

			return $user;
		}

		$username = $this->canonicalizeUsername($username);
		return $this->getRepository()->findOneBy(array('username' => $username));
	}

	/**
	 * Validates if the username and password are correct.
	 * @param string $username Username
	 * @param string $password Password
	 * @return boolean true if validation is successful, false otherwise.
	 */
	public function validateUser($username, $password)
	{
		$user = $this->getUser($username);
		if($user === null)
		{
			return false;
		}

		return $user->getPassword() === $this->hashPassword($password, $user->getHashSalt());
	}

	/**
	 * @param string $usernameOrEmail Username or email.
	 * @return User User|null Specified user or null if username and email does not exists.
	 */
	public function getUserFromUsernameOrEmail($usernameOrEmail)
	{
		$username = $this->canonicalizeUsername($usernameOrEmail);
		$email = $this->canonicalizeEmail($usernameOrEmail);
		$em = $this->getEntityManager();
		return $em->createQueryBuilder()
				->select('u')
				->from('SimpleTimeTracker\Entities\User', 'u')
				->where('u.username = :username OR u.email = :email')
				->setParameter('username', $username)
				->setParameter('email', $email)
				->getQuery()
				->getOneOrNullResult();
	}

	/**
	 * Create a new user.
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 * @param boolean $enabled
	 * @param boolean $confirmed
	 * @param string[] $roles
	 */
	public function createUser($username, $password, $email, $enabled, $confirmed, $roles)
	{
		$em = $this->getEntityManager();
		$em->beginTransaction();
		try
		{
			$user = new User();
			$user->setUsername($username);
			$user->setUsernameCanonical($this->canonicalizeUsername($username));
			$user->setEmail($email);
			$user->setEmailCanonical($this->canonicalizeEmail($email));
			$user->setDateTimeCreated(new DateTime());
			$user->setEnabled($enabled);
			$user->setIsConfirmed($confirmed);

			$user->setHashSalt(bin2hex(mcrypt_create_iv(64, MCRYPT_DEV_URANDOM)));
			$user->setPassword($this->hashPassword($password, $user->getHashSalt()));
			$user->setRoles($roles);
			if(!$confirmed)
			{
				$timeout = new DateTime();
				$timeout->add(new DateInterval('P1D'));
				$user->setDateTimeConfirmationTimeout($timeout);
				$user->setConfirmationCode(md5(serialize($user) . uniqid()));
			}
			$em->persist($user);

			$em->flush();
			$em->commit();
		}
		catch(Exception $ex)
		{
			$em->rollback();
			throw $ex;
		}
	}

	/**
	 * @param string $password
	 * @param string $salt
	 * @return string Hashed password.
	 */
	public function hashPassword($password, $salt)
	{
		return hash($this->getPasswordHashAlgorithm(), $salt . $password);
	}

	/**
	 * Change password for specified username.
	 * @param string $username Username
	 * @param string $password New password
	 * @throws TInvalidDataValueException if user does not exists.
	 */
	public function changePassword($username, $password)
	{
		$em = $this->getEntityManager();
		$em->beginTransaction();
		try
		{
			$user = $this->getUser($username);
			if($user === null)
			{
				throw new TInvalidDataValueException('User does not exists.');
			}
			$user->setPassword($this->hashPassword($password, $user->getHashSalt()));

			$em->flush();
			$em->commit();
		}
		catch(Exception $ex)
		{
			$em->rollback();
			throw $ex;
		}
	}

	/**
	 * Reset confirmation code for specified username.
	 * @param string $username Username
	 * @return string New confirmation code.
	 * @throws TInvalidDataValueException if user does not exists.
	 */
	public function resetConfirmationCode($username)
	{
		$em = $this->getEntityManager();
		$em->beginTransaction();
		try
		{
			$user = $this->getUser($username);
			if($user === null)
			{
				throw new TInvalidDataValueException('User does not exists.');
			}

			$timeout = new DateTime();
			$timeout->add(new DateInterval('P1D'));
			$user->setDateTimeConfirmationTimeout($timeout);
			$user->setConfirmationCode(md5(serialize($user) . uniqid()));

			$em->flush();
			$em->commit();
		}
		catch(Exception $ex)
		{
			$em->rollback();
			throw $ex;
		}

		return $user->getConfirmationCode();
	}

	/**
	 * Get if the confirmation code is valid for the specified username.
	 * @param string $username Username
	 * @param string $confirmationCode Confirmation code.
	 * @return bool True if the confirmation code is valid, false otherwise.
	 * @throws TInvalidDataValueException if user does not exists.
	 */
	public function getIsConfirmationCodeValid($username, $confirmationCode)
	{
		$user = $this->getUser($username);
		if($user === null)
		{
			throw new TInvalidDataValueException('User does not exists.');
		}

		$now = new DateTime();
		return $user->getConfirmationCode() == $confirmationCode && $user->getDateTimeConfirmationTimeout() > $now;
	}

	/**
	 * Send password recovery mail to specified user.
	 * @param string $usernameOrEmail
	 * @return int Number of mail sent.
	 * @throws TInvalidDataValueException if user does not exists.
	 */
	public function sendRecoverMail($usernameOrEmail)
	{
		$user = $this->getUserFromUsernameOrEmail($usernameOrEmail);
		if($user === null)
		{
			throw new TInvalidDataValueException('User does not exists.');
		}
		$confirmationCode = $this->resetConfirmationCode($user->getName());

		$params = array(
			$user->getEmail() => array(
				'{username}' => $user->getName(),
				'{url}' => $this->getRequest()->getBaseUrl() . $this->getService()->constructUrl('recover', array('u' => $user->getName(), 'code' => $confirmationCode), false),
			),
		);

		$message = Swift_Message::newInstance();
		$message->setSubject(Prado::localize('SimpleTimeTracker - Password recovery'));
		$message->addTo($user->getEmail());
		$message->setBody(Prado::localize('Dear SimpleTimeTracker user,

You receive this mail because you requested to recover your password.

Your username : {username}

Click on this link to change your password :

{url}

Thank you.'));
		$message->addPart(Prado::localize('<p>Dear SimpleTimeTracker user,</p>

<p>You receive this mail because you requested to recover your password.</p>

<p><span style="font-weight:bold">Your username :</span> {username}</p>

<p>Click on this link to change your password :</p>

<p><a href="{url}">{url}</a></p>

<p>Thank you.</p>'), 'text/html');
		Prado::trace($message->__toString(), 'Application.Controller.UserController');
		Prado::trace(print_r($params, true), 'Application.Controller.UserController');

		$mailer = $this->getApplication()->getModule('mail');
		/* @var $mailer SwiftMailer */
		$decorator = new Swift_Plugins_DecoratorPlugin($params);
		$mailer->registerPlugin($decorator);
		return $mailer->send($message);
	}

}
