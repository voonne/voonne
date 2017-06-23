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
use Voonne\Voonne\Model\Entities\Role;
use Voonne\Voonne\Model\Repositories\RoleRepository;


class RoleFacade
{

	use SmartObject;

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * @var RoleRepository
	 */
	private $roleRepository;

	/**
	 * @var array
	 */
	public $onCreate = [];

	/**
	 * @var array
	 */
	public $onUpdate = [];


	public function __construct(EntityManagerInterface $entityManager, RoleRepository $roleRepository)
	{
		$this->entityManager = $entityManager;
		$this->roleRepository = $roleRepository;
	}


	/**
	 * @param Role $role
	 *
	 * @throws DuplicateEntryException
	 */
	public function save(Role $role)
	{
		$new = ($this->entityManager->getUnitOfWork()->getEntityState($role) == UnitOfWork::STATE_NEW);

		if($this->roleRepository->countBy(['name' => $role->getName()]) != 0) {
			throw new DuplicateEntryException('Role with this name is already exists.');
		}

		$this->entityManager->persist($role);
		$this->entityManager->flush();

		if($new) {
			$this->onCreate($role);
		} else {
			$this->onUpdate($role);
		}
	}

}
