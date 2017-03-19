<?php

declare(strict_types=1);

namespace tests\AppBundle\Controller;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class TaskControllerTest.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class TaskControllerTest extends WebTestCase
{
	const BASE_URL = 'http://localhost';
	const URL = '/en/task/current';
	const LOGIN_URL = '/en/login';

	/**
	 * @var ReferenceRepository
	 */
	private static $fixture;

	protected function setUp()
	{
		parent::setUp();

		if(self::$fixture === null)
		{
			self::$fixture = $this->loadFixtures([
					'AppBundle\DataFixtures\ORM\FunctionalTestsFixture',
					], null, 'doctrine', ORMPurger::PURGE_MODE_TRUNCATE)->getReferenceRepository();
		}
	}

	public function test_current_without_role_user_redirect_to_login()
	{
		$client = static::makeClient();
		$client->request('GET', self::URL);

		$this->assertStatusCode(302, $client);
		$this->assertequals(self::BASE_URL . self::LOGIN_URL,
			$client->getResponse()->getTargetUrl());
	}

	public function test_current_without_tasks_shows_empty_task_with_start_new_controls()
	{
		$this->loginAs(self::$fixture->getReference('without-task-user'), 'main');

		$content = $this->fetchContent(self::URL);

		$this->assertNotContains('Begin:', $content);
		$this->assertNotContains('Stop', $content);
		$this->assertNotContains('Restart', $content);
		$this->assertContains('Start a new task', $content);
	}
}
