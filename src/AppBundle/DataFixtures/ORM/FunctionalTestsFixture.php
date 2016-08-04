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
class FunctionalTestsFixture extends AbstractFixture implements ContainerAwareInterface
{
	use \Symfony\Component\DependencyInjection\ContainerAwareTrait;

	public function load(ObjectManager $manager)
	{
		$user = new User();
        $user->setUsername('admin');
		$user->setEmail('test@test.com');
		$user->setRoles(['ROLE_ADMIN']);
		$encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($user, 'test');
        $user->setPassword($password);
        $manager->persist($user);

		$project = new Project();
		$project->setDescription('Unit testing');
		$project->setNo(12345);
		$project->setUser($user);
		$manager->persist($project);

		$task = new Task();
		$task->setProject($project);
		$task->setUser($user);
		$task->setDateTimeBegin(new DateTime('2016-08-05'));
		$manager->persist($task);

        $manager->flush();

		$this->addReference('admin-user', $user);
		$this->addReference('project', $project);
		$this->addReference('task', $task);
	}

}
