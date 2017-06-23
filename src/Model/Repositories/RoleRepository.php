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
use Voonne\Voonne\Model\Entities\Role;


class RoleRepository extends EntityRepository
{

	/**
	 * @param Role $role
	 * @param string $name
	 *
	 * @return bool
	 */
	public function isNameFree(Role $role, $name)
	{
		return $this->createQuery('SELECT COUNT(r) FROM ' . Role::class . ' r WHERE r.id != ?0 AND r.name = ?1')
			->setParameters([(string)$role->getId(), $name])
			->getSingleScalarResult() == 0;
	}

}
