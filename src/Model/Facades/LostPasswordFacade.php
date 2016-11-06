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
use Voonne\Voonne\Model\Entities\LostPassword;
use Voonne\Voonne\Model\Repositories\LostPasswordRepository;


class LostPasswordFacade
{

	use SmartObject;

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * @var LostPasswordRepository
	 */
	private $lostPasswordRepository;

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


	public function __construct(EntityManagerInterface $entityManager, LostPasswordRepository $lostPasswordRepository)
	{
		$this->entityManager = $entityManager;
		$this->lostPasswordRepository = $lostPasswordRepository;
	}


	/**
	 * @param LostPassword $lostPassword
	 *
	 * @throws DuplicateEntryException
	 */
	public function save(LostPassword $lostPassword)
	{
		$new = ($this->entityManager->getUnitOfWork()->getEntityState($lostPassword) == UnitOfWork::STATE_NEW);

		if($this->lostPasswordRepository->countBy(['code' => $lostPassword->getCode()]) != 0) {
			throw new DuplicateEntryException('Lost password with this code is already exists.');
		}

		$this->entityManager->persist($lostPassword);
		$this->entityManager->flush();

		if($new) {
			$this->onCreate($lostPassword);
		} else {
			$this->onUpdate($lostPassword);
		}
	}


	/**
	 * @param LostPassword $lostPassword
	 */
	public function remove(LostPassword $lostPassword)
	{
		$this->onRemove($lostPassword);

		$this->entityManager->remove($lostPassword);
		$this->entityManager->flush();
	}

}
