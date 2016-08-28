<?php

declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project.
 *
 * @Table(name="project")
 * @Entity(repositoryClass="AppBundle\Repository\ProjectRepository")
 * @UniqueEntity(fields={"no", "description", "user"}, errorPath="description")
 */
class Project
{
	/**
	 * @var int
	 *
	 * @Doctrine\ORM\Mapping\Column(name="project_id", type="integer")
	 * @Id
	 * @GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var int
	 *
	 * @Column(name="no", type="integer")
	 * @Assert\NotNull()
	 */
	private $no;

	/**
	 * @var string
	 *
	 * @Column(name="description", type="string", length=255, nullable=true)
	 * @Assert\NotBlank()
	 */
	private $description;

	/**
	 * @var string
	 *
	 * @Column(name="color", type="string", length=255, nullable=true)
	 * @Assert\Regex("/^#([0-9a-fA-F]{3}){1,2}$/", message="Please specify a color in hexadecimal format. (E.g.: #47c3d3).")
	 */
	private $color;

	/**
	 * @var bool
	 *
	 * @Column(type="boolean", nullable=false)
	 * @Assert\NotNull()
	 */
	private $pinned = false;

	/**
	 * @var User
	 * @ManyToOne(targetEntity="User")
	 * @JoinColumn(name="user_id", referencedColumnName="user_id")
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
	 * Set no.
	 *
	 * @param int $no
	 *
	 * @return Project
	 */
	public function setNo(int $no)
	{
		$this->no = $no;

		return $this;
	}

	/**
	 * Get no.
	 *
	 * @return int
	 */
	public function getNo()
	{
		return $this->no;
	}

	/**
	 * Set description.
	 *
	 * @param string $description
	 *
	 * @return Project
	 */
	public function setDescription(string $description = null)
	{
		$this->description = \AppBundle\Util\PropertyValue::ensureNullIfWhiteString($description);

		return $this;
	}

	/**
	 * Get description.
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Set color.
	 *
	 * @param string $color
	 *
	 * @return Project
	 */
	public function setColor(string $color = null)
	{
		$this->color = \AppBundle\Util\PropertyValue::ensureNullIfWhiteString($color);

		return $this;
	}

	/**
	 * Get color.
	 *
	 * @return string
	 */
	public function getColor()
	{
		return $this->color;
	}

	/**
	 * Set pinned.
	 *
	 * @param bool $pinned
	 *
	 * @return Project
	 */
	public function setPinned(bool $pinned)
	{
		$this->pinned = (bool) $pinned;

		return $this;
	}

	/**
	 * Get pinned.
	 *
	 * @return bool
	 */
	public function getPinned()
	{
		return $this->pinned;
	}

	/**
	 * Set user.
	 *
	 * @param User $user
	 *
	 * @return Project
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

	public function __toString()
	{
		$no = str_pad((string) $this->no, 5, '0', STR_PAD_LEFT);
		if($this->description == null)
		{
			return $no;
		}

		return $no . '-' . $this->description;
	}

}
