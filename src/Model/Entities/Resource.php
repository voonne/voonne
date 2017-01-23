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
 * @ORM\Entity(repositoryClass="Voonne\Voonne\Model\Repositories\ResourceRepository")
 */
class Resource
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
	 * @ORM\ManyToOne(targetEntity="Zone", inversedBy="resources", cascade={"persist"})
	 * @var Zone
	 */
	private $zone;

	/**
	 * @ORM\OneToMany(targetEntity="Privilege", mappedBy="resource")
	 * @var ArrayCollection
	 */
	private $privileges;


	public function __construct($name, $description, Zone $area)
	{
		$this->name = $name;
		$this->description = $description;
		$this->zone = $area;
		$this->privileges = new ArrayCollection();
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
	 * @return Zone
	 */
	public function getZone()
	{
		return $this->zone;
	}


	/**
	 * @return ReadOnlyCollectionWrapper
	 */
	public function getPrivileges()
	{
		return new ReadOnlyCollectionWrapper($this->privileges);
	}

}
