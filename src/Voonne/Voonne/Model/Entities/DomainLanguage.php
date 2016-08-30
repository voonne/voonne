<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\UniversallyUniqueIdentifier;
use Nette\SmartObject;


/**
 * @ORM\Entity(repositoryClass="Voonne\Voonne\Model\Repositories\DomainLanguageRepository")
 */
class DomainLanguage
{

	use SmartObject;
	use UniversallyUniqueIdentifier;

	/**
	 * @ORM\ManyToOne(targetEntity="Domain", inversedBy="domainLanguages", cascade={"persist"})
	 * @var Domain
	 */
	protected $domain;

	/**
	 * @ORM\ManyToOne(targetEntity="Language", inversedBy="domainLanguages", cascade={"persist"})
	 * @var Language
	 */
	protected $language;


	public function __construct(Domain $domain, Language $language)
	{
		$this->domain = $domain;
		$this->language = $language;
	}


	/**
	 * @return Domain
	 */
	public function getDomain()
	{
		return $this->domain;
	}


	/**
	 * @return Language
	 */
	public function getLanguage()
	{
		return $this->language;
	}

}
