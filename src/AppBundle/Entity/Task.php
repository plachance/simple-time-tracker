<?php

declare(strict_types = 1);

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Task.
 *
 * @Table(name="task")
 * @Entity(repositoryClass="AppBundle\Repository\TaskRepository")
 */
class Task
{
	/**
	 * @var int
	 *
	 * @Column(name="task_id", type="integer")
	 * @Id
	 * @GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var DateTime
	 *
	 * @Column(name="date_time_begin", type="datetime")
	 * @Assert\NotNull()
	 * @Assert\Type("\DateTime")
	 */
	private $dateTimeBegin;

	/**
	 * @var DateTime
	 *
	 * @Column(name="date_time_end", type="datetime", nullable=true)
	 * @Assert\Type("\DateTime")
	 * @Assert\Expression("value == null or value >= this.getDateTimeBegin()", message="End time must be set after begin time.")
	 */
	private $dateTimeEnd;

	/**
	 * @var Project
	 *
	 * @ManyToOne(targetEntity="Project")
	 * @JoinColumn(name="project_id", referencedColumnName="project_id", nullable=false)
	 * @Assert\NotNull()
	 */
	private $project;

	/**
	 * @var User
	 * @ManyToOne(targetEntity="User")
	 * @JoinColumn(name="user_id", referencedColumnName="user_id", nullable=false)
	 * @Assert\NotNull()
	 */
	private $user;

	/**
	 * Get id.
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set dateTimeBegin.
	 *
	 * @param DateTime $dateTimeBegin
	 *
	 * @return Task
	 */
	public function setDateTimeBegin(DateTime $dateTimeBegin)
	{
		$this->dateTimeBegin = $dateTimeBegin;

		return $this;
	}

	/**
	 * Get dateTimeBegin.
	 *
	 * @return DateTime
	 */
	public function getDateTimeBegin()
	{
		return $this->dateTimeBegin;
	}

	/**
	 * Set dateTimeEnd.
	 *
	 * @param DateTime $dateTimeEnd
	 *
	 * @return Task
	 */
	public function setDateTimeEnd(DateTime $dateTimeEnd = null)
	{
		$this->dateTimeEnd = $dateTimeEnd;

		return $this;
	}

	/**
	 * Get dateTimeEnd.
	 *
	 * @return DateTime
	 */
	public function getDateTimeEnd()
	{
		return $this->dateTimeEnd;
	}

	/**
	 * Set project.
	 *
	 * @param Project $project
	 *
	 * @return Task
	 */
	public function setProject(Project $project)
	{
		$this->project = $project;

		return $this;
	}

	/**
	 * Get project.
	 *
	 * @return Project
	 */
	public function getProject()
	{
		return $this->project;
	}

	/**
	 * Set user.
	 *
	 * @param User $user
	 *
	 * @return Task
	 */
	public function setUser(User $user)
	{
		$this->user = $user;

		return $this;
	}

	/**
	 * Get user.
	 *
	 * @return User 
	 */
	public function getUser()
	{
		return $this->user;
	}

	public function stop()
	{
		if($this->dateTimeEnd !== null)
		{
			throw new Exception\TaskAlreadyStoppedException();
		}

		$this->dateTimeEnd = new \DateTime();
	}

	/**
	 * @return \AppBundle\Entity\Task
	 */
	public function restart()
	{
		$newTask = new self();
		$newTask->setProject($this->getProject());
		$newTask->setDateTimeBegin(new \DateTime());
		$newTask->setUser($this->getUser());

		return $newTask;
	}

}
