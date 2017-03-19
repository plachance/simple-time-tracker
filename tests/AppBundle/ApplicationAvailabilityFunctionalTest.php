<?php

declare(strict_types=1);

namespace tests\AppBundle;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class ApplicationAvailabilityFunctionalTest.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
	const BASE_URL = 'http://localhost';

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

	/**
	 * @dataProvider urlProvider
	 */
	public function testPageIsSuccessful($url)
	{
		$project = self::$fixture->getReference('project');
		$task = self::$fixture->getReference('task');

		$url = str_replace([
			'{taskId}',
			'{projectId}',
			'{projectNo}',
			], [
			$task->getId(),
			$project->getId(),
			$project->getNo(),
			], $url);

		$this->loginAs(self::$fixture->getReference('admin-user'), 'main');

		$client = static::makeClient();
		$client->request('GET', $url);

		$this->isSuccessful($client->getResponse());
	}

	/**
	 * @dataProvider urlRedirectProvider
	 */
	public function testPageIsRedirected($url, $targetUrl)
	{
		$this->loginAs(self::$fixture->getReference('admin-user'), 'main');

		$client = static::makeClient();
		$client->request('GET', $url);

		$this->assertStatusCode(302, $client);
		$this->assertequals(self::BASE_URL . $targetUrl, $client->getResponse()->getTargetUrl());
	}

	public function urlProvider()
	{
		return [
			['/en/login'],
			['/en/profile'],
			['/en/project'],
			['/en/project/new'],
			['/en/project/{projectId}/edit'],
			['/en/project/{projectId}/delete'],
			['/en/project/{projectNo}/summary'],
			['/en/task'],
			['/en/task/current'],
			['/en/task/new'],
			['/en/task/{taskId}/edit'],
			['/en/task/{taskId}/delete'],
			['/en/task/timesheet'],
			['/en/task/timesheet/2016-08-05'],
			['/en/task/summary'],
			['/fr/login'],
			['/fr/profile'],
			['/fr/project'],
			['/fr/project/new'],
			['/fr/project/{projectId}/edit'],
			['/fr/project/{projectId}/delete'],
			['/fr/project/{projectNo}/summary'],
			['/fr/task'],
			['/fr/task/current'],
			['/fr/task/new'],
			['/fr/task/{taskId}/edit'],
			['/fr/task/{taskId}/delete'],
			['/fr/task/timesheet'],
			['/fr/task/timesheet/2016-08-05'],
			['/fr/task/summary'],
		];
	}

	public function urlRedirectProvider()
	{
		return [
			['/', '/en'],
			['/en', '/en/task/current'],
			['/fr', '/fr/task/current'],
		];
	}
}
