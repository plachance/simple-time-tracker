<?php

declare(strict_types = 1);

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use LogicException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class Builder.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class Builder implements ContainerAwareInterface
{
	use ContainerAwareTrait;
	/**
	 * Checks if the attributes are granted against the current authentication token and optionally supplied object.
	 *
	 * @param mixed $attributes The attributes
	 * @param mixed $object     The object
	 *
	 * @throws LogicException
	 *
	 * @return bool
	 */
	protected function isGranted($attributes, $object = null)
	{
		if(!$this->container->has('security.authorization_checker'))
		{
			throw new LogicException('The SecurityBundle is not registered in your application.');
		}

		return $this->container->get('security.authorization_checker')->isGranted($attributes,
				$object);
	}

	/**
	 * Get a user from the Security Token Storage.
	 *
	 * @throws LogicException If SecurityBundle is not available
	 *
	 * @return mixed
	 *
	 * @see TokenInterface::getUser()
	 */
	protected function getUser()
	{
		if(!$this->container->has('security.token_storage'))
		{
			throw new LogicException('The SecurityBundle is not registered in your application.');
		}

		if(null === $token = $this->container->get('security.token_storage')->getToken())
		{
			return;
		}

		if(!is_object($user = $token->getUser()))
		{
			// e.g. anonymous authentication
			return;
		}

		return $user;
	}

	/**
	 * Translate the string using the translator service.
	 *
	 * @param string $message The string to translate.
	 *
	 * @throws LogicException if the translator service is disabled.
	 *
	 * @return string The translated string.
	 */
	protected function trans(string $message)
	{
		if(!$this->container->has('translator'))
		{
			throw new LogicException('You can not use the trans method if the translator service is disabled.');
		}

		return $this->container->get('translator')->trans($message);
	}

	/**
	 * Create the main menu.
	 *
	 * @param FactoryInterface $factory
	 * @param array $options
	 *
	 * @return ItemInterface
	 */
	public function mainMenu(FactoryInterface $factory, array $options)
	{
		$menu = $factory->createItem('root',
			[
			'childrenAttributes' => [
				'class' => 'nav navbar-nav',
			],
		]);

		$label = $this->trans('Current task');
		$menu->addChild('Current',
			[
			'route' => 'task_current',
			'label' => '<span class="glyphicon glyphicon-time" aria-hidden="true"></span><span class="hidden-sm"> ' . $label . '</span>',
			'extras' => [
				'safe_label' => true,
			],
			'attributes' => [
				'title' => $label,
			],
		]);
		$label = $this->trans('History');
		$menu->addChild('History',
			[
			'route' => 'task_index',
			'label' => '<span class="glyphicon glyphicon-tasks" aria-hidden="true"></span><span class="hidden-sm"> ' . $label . '</span>',
			'extras' => [
				'safe_label' => true,
			],
			'attributes' => [
				'title' => $label,
			],
		]);
		$label = $this->trans('Timesheet');
		$menu->addChild('Timesheet',
			[
			'route' => 'task_timesheet',
			'label' => '<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span><span class="hidden-sm"> ' . $label . '</span>',
			'extras' => [
				'safe_label' => true,
			],
			'attributes' => [
				'title' => $label,
			],
		]);
		$label = $this->trans('Summary');
		$menu->addChild('Summary',
			[
			'route' => 'task_summary',
			'label' => '<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span><span class="hidden-sm"> ' . $label . '</span>',
			'extras' => [
				'safe_label' => true,
			],
			'attributes' => [
				'title' => $label,
			],
		]);
		$label = $this->trans('Projects');
		$menu->addChild('Projects',
			[
			'route' => 'project_index',
			'label' => '<span class="glyphicon glyphicon-list" aria-hidden="true"></span><span class="hidden-sm"> ' . $label . '</span>',
			'extras' => [
				'safe_label' => true,
			],
			'attributes' => [
				'title' => $label,
			],
		]);

		return $menu;
	}

	/**
	 * Create the user's menu.
	 *
	 * @param FactoryInterface $factory
	 * @param array $options
	 *
	 * @return ItemInterface
	 */
	public function userMenu(FactoryInterface $factory, array $options)
	{
		$menu = $factory->createItem('root',
			[
			'childrenAttributes' => [
				'class' => 'nav navbar-nav navbar-right',
			],
		]);

		if($this->isGranted('IS_AUTHENTICATED_REMEMBERED'))
		{
			$userMenu = $menu->addChild('User',
				[
				'uri' => '#',
				'attributes' => [
					'class' => 'dropdown',
				],
				'linkAttributes' => [
					'class' => 'dropdown-toggle',
					'data-toggle' => 'dropdown',
					'role' => 'button',
					'aria-haspopup' => 'true',
					'aria-expanded' => 'false',
				],
				'label' => '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> ' . $this->getUser()->getUsername() . ' <span class="caret"></span>',
				'extras' => [
					'safe_label' => true,
				],
				'childrenAttributes' => [
					'class' => 'dropdown-menu',
				],
			]);

			$userMenu->addChild('Profile settings',
				[
				'route' => 'profile_index',
				'label' => '<span class="glyphicon glyphicon-cog" aria-hidden="true"></span> ' . $this->trans('Profile settings'),
				'extras' => [
					'safe_label' => true,
				],
			]);

			if($this->isGranted('ROLE_PREVIOUS_ADMIN'))
			{
				$userMenu->addChild('SwitchUserExit',
					[
					'route' => 'index',
					'routeParameters' => ['_switch_user' => '_exit'],
					'label' => '<span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> ' . $this->trans('Exit impersonation'),
					'extras' => [
						'safe_label' => true,
					],
				]);
			}

			$userMenu->addChild('Logout',
				[
				'route' => 'logout',
				'label' => '<span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> ' . $this->trans('Logout'),
				'extras' => [
					'safe_label' => true,
				],
			]);
		}

		return $menu;
	}

}
