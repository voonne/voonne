<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Model\Repositories;

use Kdyby\Doctrine\EntityRepository;
use Voonne\Voonne\IOException;


abstract class Repository extends EntityRepository
{

	/**
	 * Finds an entity by id.
	 *
	 * @param mixed $id
	 * @param int $lockMode
	 * @param null $lockVersion
	 *
	 * @return object
	 *
	 * @throws IOException
	 */
	public function find($id, $lockMode = null, $lockVersion = null)
	{
		$result = parent::find($id, $lockMode, $lockVersion);

		if ($result === null) {
			throw new IOException('Not found', 404);
		}

		return $result;
	}


	/**
	 * Finds an entity by criteria array.
	 *
	 * @param array $criteria
	 * @param array $orderBy
	 *
	 * @return object
	 *
	 * @throws IOException
	 */
	public function findOneBy(array $criteria, array $orderBy = null)
	{
		$result = parent::findOneBy($criteria, $orderBy);

		if ($result === null) {
			throw new IOException('Not found', 404);
		}

		return $result;
	}

}
