<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Translator;

/**
 * Class AppController.
 *
 * It provides methods to common features needed in application's controllers.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class AppController extends Controller
{
	/**
	 * Returns a RedirectResponse to the return URL in the querystring or the given route with the given parameters.
	 *
	 * @param Request $request
	 * @param string $route The name of the route
	 * @param array  $parameters An array of parameters
	 * @param int $status The status code to use for the Response
	 * @return RedirectResponse
	 */
	protected function redirectReturnUrlOrRoute(Request $request, string $route,
		array $parameters = [], int $status = 302)
	{
		$returnUrl = $request->query->get('r');
		if($returnUrl == '')
		{
			$returnUrl = $this->generateUrl($route, $parameters);
		}

		return $this->redirect($returnUrl, $status);
	}

	/**
	 * Translate the string using the translator service.
	 *
	 * @param string $message The string to translate
	 * @param array $parameters An array of parameters for the message
	 * @param string|null $domain The domain for the message or null to use the default
	 * @param string|null $locale The locale or null to use the default
	 * @throws LogicException if the translator service is disabled
	 * @return string The translated string
	 */
	protected function trans(string $message, array $parameters = [],
		$domain = null, $locale = null)
	{
		if(!$this->container->has('translator'))
		{
			throw new LogicException('You can not use the trans method if the translator service is disabled.');
		}

		$translator = $this->container->get('translator');
		/* @var $translator Translator */
		return $translator->trans($message, $parameters, $domain, $locale);
	}

}
