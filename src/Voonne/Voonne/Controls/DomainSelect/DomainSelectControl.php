<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Controls\DomainSelect;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Voonne\Messages\FlashMessage;
use Voonne\Voonne\Controls\Control;
use Voonne\Voonne\IOException;
use Voonne\Voonne\Model\Repositories\DomainLanguageRepository;
use Voonne\Voonne\Model\Repositories\DomainRepository;
use Voonne\Voonne\Security\User;


class DomainSelectControl extends Control
{

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var Cache
	 */
	private $cache;

	/**
	 * @var DomainRepository
	 */
	private $domainRepository;

	/**
	 * @var DomainLanguageRepository
	 */
	private $domainLanguageRepository;


	public function __construct(
		User $user,
		IStorage $storage,
		DomainRepository $domainRepository,
		DomainLanguageRepository $domainLanguageRepository
	)
	{
		parent::__construct();

		$this->user = $user;
		$this->cache = new Cache($storage);
		$this->domainRepository = $domainRepository;
		$this->domainLanguageRepository = $domainLanguageRepository;
	}


	public function render()
	{
		$this->template->setFile(__DIR__ . '/DomainSelectControl.latte');

		$this->template->domains = $domains = $this->domainRepository->findAll();
		$this->template->currentDomain = $this->user->getCurrentDomainLanguage();
		$this->template->domainsCount = $this->domainLanguageRepository->countBy([]);

		$this->template->render();
	}


	public function handleDomain($id)
	{
		try {
			$this->user->setCurrentDomainLanguage($this->domainLanguageRepository->find($id));

			$this->cache->clean([Cache::TAGS => ['voonne.domainSelectControl']]);

			$this->redirect('this');
		} catch(IOException $e) {
			$this->flashMessage('common.operation.error', FlashMessage::ERROR);
			$this->redirect('this');
		}
	}

}
