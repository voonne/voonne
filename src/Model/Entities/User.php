<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Model\Entities;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Collections\ReadOnlyCollectionWrapper;
use Kdyby\Doctrine\Entities\Attributes\UniversallyUniqueIdentifier;
use Nette\Security\Passwords;
use Nette\SmartObject;


/**
 * @ORM\Entity(repositoryClass="Voonne\Voonne\Model\Repositories\UserRepository")
 */
class User
{

	use SmartObject;
	use UniversallyUniqueIdentifier;

	/**
	 * @ORM\Column(type="string", nullable=false, unique=true)
	 * @var string
	 */
	private $email;

	/**
	 * @ORM\Column(type="string", nullable=false)
	 * @var string
	 */
	private $password;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string|null
	 */
	private $firstName;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string|null
	 */
	private $lastName;

	/**
	 * @ORM\Column(type="datetime", nullable=false)
	 * @var DateTime
	 */
	private $createdAt;

	/**
	 * @ORM\ManyToMany(targetEntity="Role", inversedBy="users", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $roles;

	/**
	 * @ORM\OneToMany(targetEntity="LostPassword", mappedBy="user", orphanRemoval=true)
	 * @var ArrayCollection
	 */
	private $lostPasswords;


	public function __construct($email, $password, $firstName = null, $lastName = null)
	{
		$this->email = $email;
		$this->password = Passwords::hash($password);
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->createdAt = new DateTime();
		$this->roles = new ArrayCollection();
	}


	/**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}


	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}


	/**
	 * @return string|null
	 */
	public function getFirstName()
	{
		return $this->firstName;
	}


	/**
	 * @return string|null
	 */
	public function getLastName()
	{
		return $this->lastName;
	}


	/**
	 * @return DateTime
	 */
	public function getCreatedAt()
	{
		return $this->createdAt;
	}


	/**
	 * @return ReadOnlyCollectionWrapper
	 */
	public function getRoles()
	{
		return new ReadOnlyCollectionWrapper($this->roles);
	}


	/**
	 * @param string $email
	 * @param string|null $firstName
	 * @param string|null $lastName
	 */
	public function update($email, $firstName = null, $lastName = null)
	{
		$this->email = $email;
		$this->firstName = $firstName;
		$this->lastName = $lastName;
	}


	/**
	 * @param string $password
	 */
	public function changePassword($password)
	{
		$this->password = Passwords::hash($password);
	}


	/**
	 * @param Role $role
	 */
	public function addRole(Role $role)
	{
		$this->roles->add($role);
	}


	/**
	 * @param Role $role
	 */
	public function removeRole(Role $role)
	{
		$this->roles->removeElement($role);
	}

}
