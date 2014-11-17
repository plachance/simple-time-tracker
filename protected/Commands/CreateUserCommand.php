<?php

namespace SimpleTimeTracker\Commands;

use Exception;
use SimpleTimeTracker\Controllers\UserController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Classe CreateUserCommand.
 *
 * Commande permettant de créer un nouvel user.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class CreateUserCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('stt:user:create')
			->setDescription('Create a user.')
			->addArgument('username', InputArgument::REQUIRED)
			->addArgument('email', InputArgument::REQUIRED)
			->addArgument('role', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'User roles list.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$username = $input->getArgument('username');
		$email = $input->getArgument('email');
		$roles = $input->getArgument('role');

		$dialog = $this->getHelperSet()->get('dialog');
		/* @var $dialog DialogHelper */

		$reqValidator = function ($value) {
			if(trim($value) == '')
			{
				throw new Exception('The password must not be empty.');
			}
			return trim($value);
		};

		$password = $dialog->askHiddenResponseAndValidate(
			$output, 'Password : ', $reqValidator, 3, false
		);

		$userManager = new UserController();
		$userManager->createUser($username, $password, $email, true, true, $roles);

		$output->writeln('User created.');
	}

}
