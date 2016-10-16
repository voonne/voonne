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
use Doctrine\ORM\UnitOfWork;
use Nette\SmartObject;
use Voonne\Voonne\DuplicateEntryException;
use Voonne\Voonne\Model\Entities\User;
use Voonne\Voonne\Model\Repositories\UserRepository;


class UserFacade
{

	use SmartObject;

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * @var UserRepository
	 */
	private $userRepository;

	/**
	 * @var array
	 */
	public $onCreate = [];

	/**
	 * @var array
	 */
	public $onUpdate = [];

	/**
	 * @var array
	 */
	public $onRemove = [];


	public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
	{
		$this->entityManager = $entityManager;
		$this->userRepository = $userRepository;
	}


	/**
	 * @param User $user
	 *
	 * @throws DuplicateEntryException
	 */
	public function save(User $user)
	{
		$new = ($this->entityManager->getUnitOfWork()->getEntityState($user) == UnitOfWork::STATE_NEW);

		if (!$this->userRepository->isEmailFree($user, $user->getEmail())) {
			throw new DuplicateEntryException('User with this email is already exists.');
		}

		$this->entityManager->persist($user);
		$this->entityManager->flush();

		if($new) {
			$this->onCreate($user);
		} else {
			$this->onUpdate($user);
		}
	}


	/**
	 * @param User $user
	 */
	public function remove(User $user)
	{
		$this->entityManager->remove($user);
		$this->entityManager->flush();

		$this->onRemove();
	}

}
