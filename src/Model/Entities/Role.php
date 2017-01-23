<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Model\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Collections\ReadOnlyCollectionWrapper;
use Kdyby\Doctrine\Entities\Attributes\UniversallyUniqueIdentifier;
use Nette\SmartObject;


/**
 * @ORM\Entity(repositoryClass="Voonne\Voonne\Model\Repositories\RoleRepository")
 */
class Role
{

	use SmartObject;
	use UniversallyUniqueIdentifier;

	/**
	 * @ORM\Column(type="string", nullable=false)
	 * @var string
	 */
	private $name;

	/**
	 * @ORM\ManyToMany(targetEntity="Privilege", inversedBy="roles", cascade={"persist"})
	 * @var ArrayCollection
	 */
	private $privileges;

	/**
	 * @ORM\ManyToMany(targetEntity="User", mappedBy="roles")
	 * @var ArrayCollection
	 */
	private $users;


	public function __construct($name)
	{
		$this->name = $name;
		$this->privileges = new ArrayCollection();
		$this->users = new ArrayCollection();
	}


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * @return ReadOnlyCollectionWrapper
	 */
	public function getPrivileges()
	{
		return new ReadOnlyCollectionWrapper($this->privileges);
	}


	/**
	 * @return ReadOnlyCollectionWrapper
	 */
	public function getUsers()
	{
		return new ReadOnlyCollectionWrapper($this->users);
	}


	/**
	 * @param Privilege $privilege
	 */
	public function addPrivilege(Privilege $privilege)
	{
		$this->privileges->add($privilege);
	}


	/**
	 * @param Privilege $privilege
	 */
	public function removePrivilege(Privilege $privilege)
	{
		$this->privileges->removeElement($privilege);
	}


	/**
	 * @param string $name
	 */
	public function update($name)
	{
		$this->name = $name;
	}

}
