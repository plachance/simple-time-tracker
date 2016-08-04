<?php

namespace tests\AppBundle\Entity;

use AppBundle\Entity\Project;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Class TaskTest.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class TaskTest extends TestCase
{
	const PAST_DATETIME = '2000-01-02 02:04:05';

	public function test_stop_sets_the_dateTimeEnd_to_now()
	{
		$task = new Task();

		$task->stop();

		$result = $task->getDateTimeEnd();
		$now = (new DateTime())->getTimestamp();

		$this->assertNotNull($result);
		$this->assertGreaterThanOrEqual($now - 1, $result->getTimestamp());
		$this->assertLessThanOrEqual($now + 1, $result->getTimestamp());
	}

	public function test_throws_TaskAlreadyStoppedException_if_already_stopped()
	{
		$this->expectException('AppBundle\Entity\Exception\TaskAlreadyStoppedException');

		$task = new Task();

		$task->stop();
		$task->stop();
	}

	public function test_restart_returns_a_new_task()
	{
		$task = new Task();
		$task->setProject($this->createMock(Project::class));
		$task->setUser($this->createMock(User::class));

		$result = $task->restart();

		$this->assertNotSame($task, $result);
	}

	/**
	 * @depends test_restart_returns_a_new_task
	 */
	public function test_restart_share_the_project_with_new_task()
	{
		$project = $this->createMock(Project::class);

		$task = new Task();
		$task->setProject($project);
		$task->setUser($this->createMock(User::class));

		$result = $task->restart()->getProject();

		$this->assertSame($project, $result);
	}

	/**
	 * @depends test_restart_returns_a_new_task
	 */
	public function test_restart_share_the_user_with_new_task()
	{
		$user = $this->createMock(User::class);

		$task = new Task();
		$task->setProject($this->createMock(Project::class));
		$task->setUser($user);

		$result = $task->restart()->getUser();

		$this->assertSame($user, $result);
	}

	public function test_restart_sets_the_dateTimeBegin_of_new_task_to_now()
	{
		$task = new Task();

		$task->setDateTimeBegin(new DateTime(self::PAST_DATETIME));
		$task->setProject($this->createMock(Project::class));
		$task->setUser($this->createMock(User::class));

		$result = $task->restart()->getDateTimeBegin();
		$now = (new DateTime())->getTimestamp();

		$this->assertNotNull($result);
		$this->assertGreaterThanOrEqual($now - 1, $result->getTimestamp());
		$this->assertLessThanOrEqual($now + 1, $result->getTimestamp());
	}

	public function test_restart_doesnt_set_dateTimeEnd_of_new_task()
	{
		$task = new Task();

		$task->setProject($this->createMock(Project::class));
		$task->setUser($this->createMock(User::class));

		$result = $task->restart()->getDateTimeEnd();

		$this->assertNull($result);
	}

}
