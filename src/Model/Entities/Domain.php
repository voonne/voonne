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
 * @ORM\Entity(repositoryClass="Voonne\Voonne\Model\Repositories\DomainRepository")
 */
class Domain
{

	use SmartObject;
	use UniversallyUniqueIdentifier;

	/**
	 * @ORM\Column(type="string", nullable=false, unique=true)
	 * @var string
	 */
	private $name;

	/**
	 * @ORM\OneToMany(targetEntity="DomainLanguage", mappedBy="domain")
	 * @var ArrayCollection
	 */
	private $domainLanguages;


	public function __construct($name)
	{
		$this->name = $name;
		$this->domainLanguages = new ArrayCollection();
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
	public function getDomainLanguages()
	{
		return new ReadOnlyCollectionWrapper($this->domainLanguages);
	}

}
