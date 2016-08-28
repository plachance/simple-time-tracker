<?php

declare(strict_types=1);

namespace AppBundle\Util;

use RuntimeException;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\RecursiveValidator;

/**
 * Trait PasswordQuestionHelper.
 *
 * Offer features to ask a password with or without confirmation in a console Command.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
trait PasswordQuestionHelper
{
	protected function askPassword(InputInterface $input, OutputInterface $output,
		string $label = 'Password: ')
	{
		$helper = $this->getHelper('question');
		/* @var $helper QuestionHelper */

		$validator = $this->getContainer()->get('validator');
		/* @var $validator RecursiveValidator */
		$passwordQuestion = new Question($label);
		$passwordQuestion->setValidator(function ($answer) use ($validator)
		{
			$notBlank = new NotBlank();
			$errors = $validator->validate($answer, $notBlank);
			/* @var $errors ConstraintViolationList|ConstraintViolation[] */
			if($errors->count() > 0)
			{
				throw new RuntimeException($errors[0]->getMessage());
			}

			return $answer;
		});
		$passwordQuestion->setHidden(true);
		$passwordQuestion->setHiddenFallback(false);

		return $helper->ask($input, $output, $passwordQuestion);
	}

	protected function askPasswordWithConfirmation(InputInterface $input,
		OutputInterface $output, string $label = 'Password: ')
	{
		do
		{
			$password = $this->askPassword($input, $output, $label);

			try
			{
				$confirmPassword = $this->askPasswordConfirmation($input, $output, $password);
			}
			catch(RuntimeException $ex)
			{
				$output->writeln('<error>' . $ex->getMessage() . '</error>');
				$confirmPassword = null;
			}
		}
		while(null === $confirmPassword);

		return $password;
	}

	protected function askPasswordConfirmation(InputInterface $input,
		OutputInterface $output, string $password)
	{
		$helper = $this->getHelper('question');
		/* @var $helper QuestionHelper */

		$confirmPasswordQuestion = new Question('Confirm password: ');
		$confirmPasswordQuestion->setValidator(function ($value) use ($password)
		{
			if($value !== $password)
			{
				throw new RuntimeException('The confirmation does not match the password.');
			}

			return $value;
		});
		$confirmPasswordQuestion->setHidden(true);
		$confirmPasswordQuestion->setHiddenFallback(false);
		$confirmPasswordQuestion->setMaxAttempts(1);

		return $helper->ask($input, $output, $confirmPasswordQuestion);
	}

}
