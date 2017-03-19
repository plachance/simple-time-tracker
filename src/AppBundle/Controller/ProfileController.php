<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\PreferencesType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Class ProfileController.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 * @Route("/profile")
 */
class ProfileController extends AppController
{
	/**
	 * Show the profile settings forms.
	 *
	 * @Route("", name="profile_index")
	 * @Method({"GET", "POST"})
	 * @Security("is_granted(['ROLE_USER', 'IS_AUTHENTICATED_FULLY'])")
	 */
	public function indexAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();

		$user = $this->getUser();
		/* @var $user User */
		$em->refresh($user); //It tries to save the user with an empty password otherwise.

		$formPreferences = $this->createForm(PreferencesType::class, $user);
		$formPreferences->handleRequest($request);

		if($formPreferences->isSubmitted() && $formPreferences->isValid())
		{
			$em->flush();

			$this->addFlash('success', $this->trans('Preferences saved.'));

			return $this->redirectToRoute('profile_index');
		}

		$formChangePassword = $this->createChangePasswordForm();
		$formChangePassword->handleRequest($request);

		if($formChangePassword->isSubmitted() && $formChangePassword->isValid())
		{
			$encoder = $this->container->get('security.password_encoder');
			/* @var $encoder UserPasswordEncoderInterface */

			$password = $formChangePassword->get('password')->getData();
			$user->setPassword($encoder->encodePassword($user, $password));
			$em->flush();

			$this->addFlash('success', $this->trans('Password changed.'));

			return $this->redirectToRoute('profile_index');
		}

		return $this->render('profile/index.html.twig',
				[
				'form_preferences' => $formPreferences->createView(),
				'form_change_password' => $formChangePassword->createView(),
		]);
	}

	/**
	 * Creates a form to change a user's password.
	 *
	 * @return Form The form
	 */
	private function createChangePasswordForm()
	{
		return $this->createFormBuilder()
				->setAction($this->generateUrl('profile_index'))
				->add('password', RepeatedType::class,
					[
					'type' => PasswordType::class,
					'invalid_message' => 'The password fields must match.',
					'first_options' => [
						'label' => 'Password:',
					],
					'second_options' => [
						'label' => 'Repeat Password:',
					],
					'constraints' => new Length(['min' => 8]),
				])
				->add('submit', SubmitType::class,
					[
					'label' => 'Save',
					'attr' => [
						'class' => 'btn btn-primary',
					],
				])
				->getForm()
		;
	}
}
