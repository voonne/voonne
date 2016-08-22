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
use Nette\Security\Passwords;


/**
 * @ORM\Entity(repositoryClass="Voonne\Voonne\Model\Repositories\UserRepository")
 */
class User
{

	use UniversallyUniqueIdentifier;

	/**
	 * @ORM\Column(type="string", length=200, nullable=false, unique=true)
	 * @var string
	 */
	private $email;

	/**
	 * @ORM\Column(type="string", length=60, nullable=false)
	 * @var string
	 */
	private $password;

	/**
	 * @ORM\Column(type="datetime", nullable=false)
	 * @var DateTime
	 */
	protected $createdAt;


	public function __construct($email, $password)
	{
		$this->email = $email;
		$this->password = Passwords::hash($password);
		$this->createdAt = new DateTime();
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
	 * @return DateTime
	 */
	public function getCreatedAt()
	{
		return $this->createdAt;
	}

}
