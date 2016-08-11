<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Model\Facades;

use Doctrine\ORM\EntityManagerInterface;
use Nette\Object;
use Voonne\Voonne\Model\Entities\User;


class UserFacade extends Object
{

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;


	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}


	public function save(User $user)
	{
		$this->entityManager->persist($user);
		$this->entityManager->flush();
	}


	public function remove(User $user)
	{
		$this->entityManager->remove($user);
		$this->entityManager->flush();
	}

}
