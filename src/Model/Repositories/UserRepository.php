<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Model\Repositories;

use Voonne\Model\EntityRepository;
use Voonne\Voonne\Model\Entities\User;


class UserRepository extends EntityRepository
{

	/**
	 * @param User $user
	 * @param string $email
	 *
	 * @return bool
	 */
	public function isEmailFree(User $user, $email)
	{
		return $this->createQuery('SELECT COUNT(u) FROM ' . User::class . ' u WHERE u.id != ?0 AND u.email = ?1')
			->setParameters([(string)$user->getId(), $email])
			->getSingleScalarResult() == 0;
	}

}
