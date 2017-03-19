<?php

declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User.
 *
 * @Table(name="`user`")
 * @Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity(fields={"username"})
 * @UniqueEntity(fields={"email"})
 */
class User implements AdvancedUserInterface, Serializable
{
	/**
	 * @var int
	 *
	 * @Column(name="user_id", type="integer")
	 * @Id
	 * @GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @Column(type="string", length=255, unique=true)
	 * @Assert\NotBlank()
	 */
	private $username;

	/**
	 * @Column(type="string", length=255)
	 * @Assert\NotBlank()
	 */
	private $password;

	/**
	 * @Column(type="string", length=255, unique=true)
	 * @Assert\NotBlank()
	 * @Assert\Email()
	 */
	private $email;

	/**
	 * @var array
	 * @Column(name="roles", type="json_array", nullable=true)
	 */
	private $roles = ['ROLE_USER'];

	/**
	 * @Column(name="is_active", type="boolean")
	 * @Assert\NotNull()
	 */
	private $isActive = true;

	/**
	 * @var float
	 * @Column(name="day_length", type="float", nullable=false)
	 * @Assert\NotNull()
	 * @Assert\Type("double")
	 * @Assert\Range(min=0.01, max=23.99)
	 */
	private $dayLength = 7.5;

	/**
	 * @var bool
	 * @Column(name="projects_order_by_asc", type="boolean", nullable=false)
	 * @Assert\NotNull()
	 */
	private $projectsOrderByAsc = true;

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
	 * @param string $username
	 *
	 * @return User
	 */
	public function setUsername(string $username)
	{
		$this->username = $username;

		return $this;
	}

	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @param string $password
	 *
	 * @return User
	 */
	public function setPassword(string $password)
	{
		$this->password = $password;

		return $this;
	}

	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @param string $email
	 *
	 * @return User
	 */
	public function setEmail(string $email)
	{
		$this->email = $email;

		return $this;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function eraseCredentials()
	{
		$this->password = null;
	}

	/**
	 * @param array $roles
	 *
	 * @return \AppBundle\Entity\User
	 */
	public function setRoles(array $roles)
	{
		$this->roles = $roles;

		return $this;
	}

	public function getRoles()
	{
		return $this->roles;
	}

	public function getSalt()
	{
		return;
	}

	public function isAccountNonExpired()
	{
		return true;
	}

	public function isAccountNonLocked()
	{
		return true;
	}

	public function isCredentialsNonExpired()
	{
		return true;
	}

	/**
	 * @param bool $enabled
	 *
	 * @return \AppBundle\Entity\User
	 */
	public function setEnabled(bool $enabled)
	{
		$this->isActive = $enabled;

		return $this;
	}

	public function isEnabled()
	{
		return $this->isActive;
	}

	/**
	 * @param string $daylength
	 *
	 * @return User
	 */
	public function setDayLength(float $daylength)
	{
		$this->dayLength = $daylength;

		return $this;
	}

	public function getDayLength()
	{
		return $this->dayLength;
	}

	/**
	 * @return bool
	 */
	public function getProjectsOrderByAsc(): bool
	{
		return $this->projectsOrderByAsc;
	}

	/**
	 * @param bool $projectsOrderByAsc
	 *
	 * @return User
	 */
	public function setProjectsOrderByAsc(bool $projectsOrderByAsc = true): User
	{
		$this->projectsOrderByAsc = $projectsOrderByAsc;

		return $this;
	}

	public function serialize()
	{
		return serialize([
			$this->id,
			$this->username,
			$this->password,
			$this->roles,
			$this->isActive,
		]);
	}

	public function unserialize($serialized)
	{
		list(
			$this->id,
			$this->username,
			$this->password,
			$this->roles,
			$this->isActive) = unserialize($serialized);
	}

	public function __toString()
	{
		return $this->getUsername();
	}

}
