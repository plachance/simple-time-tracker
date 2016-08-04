<?php

declare(strict_types = 1);

namespace AppBundle\Command;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\RecursiveValidator;

/**
 * Class ChangePasswordCommand.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class ChangePasswordCommand extends ContainerAwareCommand
{
	use \AppBundle\Util\PasswordQuestionHelper;
	protected function configure()
	{
		$this->setName('user:change-password')
			->setDescription('Change user\'s password.')
			->addArgument('username', InputArgument::REQUIRED, 'Username')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$username = $input->getArgument('username');

		$em = $this->getContainer()->get('doctrine')->getManager();
		/* @var $em EntityManagerInterface */

		$user = $em->getRepository(User::class)->findOneBy(['username' => $username]);
		if($user === null)
		{
			$output->writeln("<error>Username '{$username}' not found.</error>");

			return 1;
		}

		$encoder = $this->getContainer()->get('security.password_encoder');
		/* @var $encoder UserPasswordEncoderInterface */

		$password = $this->askPasswordWithConfirmation($input, $output,
			'New password: ');
		$user->setPassword($encoder->encodePassword($user, $password));

		$validator = $this->getContainer()->get('validator');
		/* @var $validator RecursiveValidator */
		$errors = $validator->validate($user);
		if(count($errors) > 0)
		{
			throw new RuntimeException((string) $errors);
		}

		$em->flush();

		$output->writeln("<info>Password changed for user '{$username}'.</info>");
	}

}
