<?php

namespace SimpleTimeTracker\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 */
class Project
{
    /**
     * @var integer
     */
    private $no;

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
     * Set no
     *
     * @param integer $no
     * @return Project
     */
    public function setNo($no)
    {
        $this->no = $no;

        return $this;
    }

    /**
     * Get no
     *
     * @return integer 
     */
    public function getNo()
    {
        return $this->no;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Project
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
     * @return Project
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

	public function __toString()
	{
		$no = str_pad($this->no, 5, '0', STR_PAD_LEFT);
		if($this->description == null)
		{
			return $no;
		}
		return $no . '-' . $this->description;
	}

}
