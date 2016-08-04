<?php

declare(strict_types = 1);

namespace AppBundle\Command;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\RecursiveValidator;

/**
 * Class CreateUserCommand.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class CreateUserCommand extends ContainerAwareCommand
{
	use \AppBundle\Util\PasswordQuestionHelper;
	protected function configure()
	{
		$this->setName('user:create')
			->setDescription('Create a user')
			->addArgument('username', InputArgument::REQUIRED, 'Username')
			->addOption('email', null, InputOption::VALUE_REQUIRED, 'Email')
			->addOption('roles', 'r',
				InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Roles',
				['ROLE_USER'])
		;
	}

	protected function askEmail(InputInterface $input, OutputInterface $output)
	{
		$helper = $this->getHelper('question');
		/* @var $helper QuestionHelper */

		$validator = $this->getContainer()->get('validator');
		/* @var $validator RecursiveValidator */
		$emailQuestion = new Question('Email address: ');
		$emailQuestion->setValidator(function ($answer) use ($validator)
		{
			$emailConstraint = new Email();
			$notBlank = new NotBlank();
			$errors = $validator->validate($answer, [$notBlank, $emailConstraint]);
			/* @var $errors ConstraintViolationList|ConstraintViolation[] */
			if($errors->count() > 0)
			{
				throw new RuntimeException($errors[0]->getMessage());
			}

			return $answer;
		});

		return $helper->ask($input, $output, $emailQuestion);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$username = $input->getArgument('username');
		$email = $input->getOption('email') ?? $this->askEmail($input, $output);
		$password = $this->askPasswordWithConfirmation($input, $output);
		$roles = $input->getOption('roles');

		$this->createUser($username, $email, $password, $roles);

		$output->writeln("<info>User '{$username}' created.</info>");
	}

	/**
	 * Create a user with the provided informations.
	 *
	 * @param string $username
	 * @param string $email
	 * @param string $password
	 * @param array $roles
	 *
	 * @throws RuntimeException if there were validation errors.
	 */
	protected function createUser(string $username, string $email,
		string $password, array $roles)
	{
		$em = $this->getContainer()->get('doctrine')->getManager();
		/* @var $em EntityManagerInterface */

		$encoder = $this->getContainer()->get('security.password_encoder');
		/* @var $encoder UserPasswordEncoderInterface */

		$user = new User();
		$user->setUsername($username);
		$user->setEmail($email);
		$user->setPassword($encoder->encodePassword($user, $password));
		$user->setRoles($roles);

		$validator = $this->getContainer()->get('validator');
		/* @var $validator RecursiveValidator */
		$errors = $validator->validate($user);
		if(count($errors) > 0)
		{
			throw new RuntimeException((string) $errors);
		}

		$em->persist($user);
		$em->flush();
	}

}
