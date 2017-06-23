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
use Voonne\Voonne\Model\Entities\LostPassword;


class LostPasswordRepository extends EntityRepository
{

	/**
	 * @param LostPassword $lostPassword
	 * @param string $code
	 *
	 * @return bool
	 */
	public function isCodeFree(LostPassword $lostPassword, $code)
	{
		return $this->createQuery('SELECT COUNT(lp) FROM ' . LostPassword::class . ' lp WHERE lp.id != ?0 AND lp.code = ?1')
			->setParameters([(string)$lostPassword->getId(), $code])
			->getSingleScalarResult() == 0;
	}

}
