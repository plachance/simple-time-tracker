<?php

use SimpleTimeTracker\Controllers\UserController;
use SimpleTimeTracker\Entities\User;

/**
 * Class XUserManager.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class XUserManager extends TModule implements IUserManager
{
	private $guestName = UserController::GUEST_USERNAME;

	/**
	 * @return string guest name, defaults to 'Guest'
	 */
	public function getGuestName()
	{
		return $this->guestName;
	}

	/**
	 * @param string name to be used for guest users.
	 */
	public function setGuestName($value)
	{
		$this->guestName = $value;
	}

	/**
	 * @param string $username
	 * @return User|null Specified user or null if username does not exists.
	 */
	public function getUser($username = null)
	{
		$ctrl = new UserController();
		return $ctrl->getUser($username);
	}

	/**
	 * Returns a user instance according to auth data stored in a cookie.
	 * @param THttpCookie $cookie
	 * @return null|IUser The user instance generated based on the cookie auth data, null if the cookie does not have valid auth data.
	 */
	public function getUserFromCookie($cookie)
	{
		if(($data = $cookie->getValue()) !== '')
		{
			$app = $this->getApplication();
			if(($data = $app->getSecurityManager()->validateData($data)) !== false)
			{
				$data = unserialize($data);
				if(is_array($data) && count($data) == 3)
				{
					list($username, $address, $token) = $data;
					if(($user = $this->getUser($username)) !== null && $address == $app->getRequest()->getUserHostAddress() && $token == $user->getPassword())
					{
						return $user;
					}
				}
			}
		}
		return null;
	}

	/**
	 * Saves user auth data into a cookie.
	 * @param THttpCookie $cookie The cookie to receive the user auth data.
	 */
	public function saveUserToCookie($cookie)
	{
		$app = $this->getApplication();
		$user = $app->getUser();
		/* @var $user User */
		$username = $user->getName();
		$address = $app->getRequest()->getUserHostAddress();
		$token = $user->getPassword();
		$data = array($username, $address, $token);
		$data = serialize($data);
		$data = $app->getSecurityManager()->hashData($data);
		$cookie->setValue($data);
	}

	/**
	 * Validates if the username and password are correct.
	 * @param string $username Username
	 * @param string $password Password
	 * @return boolean true if validation is successful, false otherwise.
	 */
	public function validateUser($username, $password)
	{
		$ctrl = new UserController();
		return $ctrl->validateUser($username, $password);
	}

}
