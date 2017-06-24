<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Model\Repositories;

use Doctrine\ORM\NoResultException;
use Voonne\Model\EntityRepository;
use Voonne\Model\IOException;
use Voonne\Voonne\Model\Entities\Privilege;
use Voonne\Voonne\Model\Entities\Resource;
use Voonne\Voonne\Model\Entities\Zone;


class PrivilegeRepository extends EntityRepository
{

	public function getPrivilege($zone, $resource, $privilege)
	{
		try {
			return $this->createQuery('SELECT p FROM ' . Privilege::class . ' p 
				WHERE p.name = :privilege AND p.resource = (SELECT r FROM ' . Resource::class . ' r 
				WHERE r.name = :resource AND r.zone = (SELECT z FROM ' . Zone::class . ' z
				WHERE z.name = :zone))')
				->setParameters(['zone' => $zone, 'resource' => $resource, 'privilege' => $privilege])
				->getSingleResult();
		} catch (NoResultException $e) {
			throw new IOException();
		}
	}

}
