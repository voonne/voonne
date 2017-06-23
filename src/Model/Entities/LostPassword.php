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
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\UniversallyUniqueIdentifier;
use Nette\SmartObject;
use Nette\Utils\Random;


/**
 * @ORM\Entity(repositoryClass="Voonne\Voonne\Model\Repositories\LostPasswordRepository")
 */
class LostPassword
{

	use SmartObject;
	use UniversallyUniqueIdentifier;

	/**
	 * @ORM\Column(type="string", nullable=false, unique=true)
	 * @var string
	 */
	protected $code;

	/**
	 * @ORM\Column(type="datetime", nullable=false)
	 * @var DateTime
	 */
	protected $createdAt;

	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="lostPasswords", cascade={"persist"})
	 * @var User
	 */
	protected $user;


	public function __construct(User $user)
	{
		$this->code = Random::generate(10);
		$this->createdAt = new DateTime();
		$this->user = $user;
	}


	/**
	 * @return string
	 */
	public function getCode()
	{
		return $this->code;
	}


	/**
	 * @return DateTime
	 */
	public function getCreatedAt()
	{
		return $this->createdAt;
	}


	/**
	 * @return User
	 */
	public function getUser()
	{
		return $this->user;
	}

}
