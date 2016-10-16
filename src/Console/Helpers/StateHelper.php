<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file LICENCE.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Console\Helpers;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaValidator;
use Symfony\Component\Console\Helper\Helper;
use Voonne\Voonne\Model\Repositories\LanguageRepository;


class StateHelper extends Helper
{

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * @var LanguageRepository
	 */
	private $languageRepository;


	public function __construct(EntityManagerInterface $entityManager, LanguageRepository $languageRepository)
	{
		$this->entityManager = $entityManager;
		$this->languageRepository = $languageRepository;
	}


	public function isInstalled()
	{
		$validator = new SchemaValidator($this->entityManager);

		if(!$validator->schemaInSyncWithMetadata()) {
			return false;
		}

		if($this->languageRepository->countBy([]) == 0) {
			return false;
		}

		return true;
	}


	public function getName()
	{
		return 'state';
	}

}
