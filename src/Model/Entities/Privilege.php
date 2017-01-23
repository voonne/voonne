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
 * @ORM\Entity(repositoryClass="Voonne\Voonne\Model\Repositories\PrivilegeRepository")
 */
class Privilege
{

	use SmartObject;
	use UniversallyUniqueIdentifier;

	/**
	 * @ORM\Column(type="string", nullable=false)
	 * @var string
	 */
	private $name;

	/**
	 * @ORM\Column(type="string", nullable=false)
	 * @var string
	 */
	private $description;

	/**
	 * @ORM\ManyToOne(targetEntity="Resource", inversedBy="privileges", cascade={"persist"})
	 * @var Resource
	 */
	private $resource;

	/**
	 * @ORM\ManyToMany(targetEntity="Role", mappedBy="privileges")
	 * @var ArrayCollection
	 */
	private $roles;


	public function __construct($name, $description, Resource $resource)
	{
		$this->name = $name;
		$this->description = $description;
		$this->resource = $resource;
		$this->roles = new ArrayCollection();
	}


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}


	/**
	 * @return Resource
	 */
	public function getResource()
	{
		return $this->resource;
	}


	/**
	 * @return ReadOnlyCollectionWrapper
	 */
	public function getRoles()
	{
		return new ReadOnlyCollectionWrapper($this->roles);
	}

}
