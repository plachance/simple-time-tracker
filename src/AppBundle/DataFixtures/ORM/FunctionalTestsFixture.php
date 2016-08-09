<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Project;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Class FunctionalTestsFixture.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class FunctionalTestsFixture extends AbstractFixture
	implements ContainerAwareInterface
{
	use \Symfony\Component\DependencyInjection\ContainerAwareTrait;
	public function load(ObjectManager $manager)
	{
		$encoder = $this->container->get('security.password_encoder');

		$userAdmin = new User();
		$userAdmin->setUsername('admin');
		$userAdmin->setEmail('test@test.com');
		$userAdmin->setRoles(['ROLE_ADMIN']);
		$userAdmin->setPassword($encoder->encodePassword($userAdmin, 'test'));
		$manager->persist($userAdmin);
		$this->addReference('admin-user', $userAdmin);

		$userWithoutTask = new User();
		$userWithoutTask->setUsername('Withouttask');
		$userWithoutTask->setEmail('Withouttask@test.com');
		$userWithoutTask->setRoles(['ROLE_USER']);
		$userWithoutTask->setPassword($encoder->encodePassword($userWithoutTask,
				'test'));
		$manager->persist($userWithoutTask);
		$this->addReference('without-task-user', $userWithoutTask);

		$project = new Project();
		$project->setDescription('Unit testing');
		$project->setNo(12345);
		$project->setUser($userAdmin);
		$manager->persist($project);
		$this->addReference('project', $project);

		$task = new Task();
		$task->setProject($project);
		$task->setUser($userAdmin);
		$task->setDateTimeBegin(new DateTime('2016-08-05'));
		$manager->persist($task);
		$this->addReference('task', $task);

		$manager->flush();
	}

}
