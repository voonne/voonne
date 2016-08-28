<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Security;

use Nette\SmartObject;
use Voonne\Voonne\InvalidStateException;
use Voonne\Voonne\Model\Repositories\UserRepository;


class User
{

	use SmartObject;

	/**
	 * @var \Nette\Security\User
	 */
	private $user;

	/**
	 * @var UserRepository
	 */
	private $userRepository;


	public function __construct(\Nette\Security\User $user, UserRepository $userRepository)
	{
		$this->user = $user;
		$this->userRepository = $userRepository;
	}


	/**
	 * Returns entity of signed in user.
	 *
	 * @return \Voonne\Voonne\Model\Entities\User
	 *
	 * @throws InvalidStateException
	 */
	public function getUser()
	{
		if(!$this->user->isLoggedIn()) {
			throw new InvalidStateException('The user is not signed in.');
		}

		return $this->userRepository->find($this->user->getId());
	}

}
