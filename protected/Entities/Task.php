<?php

namespace SimpleTimeTracker\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Task
 */
class Task
{
    /**
     * @var \DateTime
     */
    private $dateTimeBegin;

    /**
     * @var \DateTime
     */
    private $dateTimeEnd;

    /**
     * @var string
     */
    private $description;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \SimpleTimeTracker\Entities\User
     */
    private $user;

    /**
     * @var \SimpleTimeTracker\Entities\Project
     */
    private $project;


    /**
     * Set dateTimeBegin
     *
     * @param \DateTime $dateTimeBegin
     * @return Task
     */
    public function setDateTimeBegin($dateTimeBegin)
    {
        $this->dateTimeBegin = $dateTimeBegin;

        return $this;
    }

    /**
     * Get dateTimeBegin
     *
     * @return \DateTime 
     */
    public function getDateTimeBegin()
    {
        return $this->dateTimeBegin;
    }

    /**
     * Set dateTimeEnd
     *
     * @param \DateTime $dateTimeEnd
     * @return Task
     */
    public function setDateTimeEnd($dateTimeEnd)
    {
        $this->dateTimeEnd = $dateTimeEnd;

        return $this;
    }

    /**
     * Get dateTimeEnd
     *
     * @return \DateTime 
     */
    public function getDateTimeEnd()
    {
        return $this->dateTimeEnd;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Task
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \SimpleTimeTracker\Entities\User $user
     * @return Task
     */
    public function setUser(\SimpleTimeTracker\Entities\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \SimpleTimeTracker\Entities\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set project
     *
     * @param \SimpleTimeTracker\Entities\Project $project
     * @return Task
     */
    public function setProject(\SimpleTimeTracker\Entities\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \SimpleTimeTracker\Entities\Project 
     */
    public function getProject()
    {
        return $this->project;
    }
}
