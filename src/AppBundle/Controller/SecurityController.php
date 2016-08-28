<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class SecurityController.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class SecurityController extends Controller
{
	/**
	 * @Route("/login", name="login")
	 * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
	 */
	public function loginAction()
	{
		$authenticationUtils = $this->get('security.authentication_utils');
		$error = $authenticationUtils->getLastAuthenticationError();
		$lastUsername = $authenticationUtils->getLastUsername();

		return $this->render(
				'security/login.html.twig',
				[
				'last_username' => $lastUsername,
				'error' => $error,
				]
		);
	}

}
