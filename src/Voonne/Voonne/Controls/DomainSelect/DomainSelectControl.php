<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Controls\DomainSelect;

use Voonne\Voonne\Controls\Control;
use Voonne\Voonne\IOException;
use Voonne\Voonne\Messages\FlashMessage;
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
	 * @var DomainRepository
	 */
	private $domainRepository;

	/**
	 * @var DomainLanguageRepository
	 */
	private $domainLanguageRepository;


	public function __construct(
		User $user,
		DomainRepository $domainRepository,
		DomainLanguageRepository $domainLanguageRepository
	)
	{
		parent::__construct();

		$this->user = $user;
		$this->domainRepository = $domainRepository;
		$this->domainLanguageRepository = $domainLanguageRepository;
	}


	public function render()
	{
		$this->template->setFile(__DIR__ . '/DomainSelectControl.latte');

		$this->template->domains = $domains = $this->domainRepository->findAll();
		$this->template->currentDomain = $this->user->getCurrentDomainLanguage();

		$this->template->domainsCount = 0;

		foreach($domains as $domain) {
			foreach($domain->getDomainLanguages() as $domainLanguage) {
				$this->template->domainsCount++;
			}
		}

		$this->template->render();
	}


	public function handleDomain($id)
	{
		try {
			$this->user->setCurrentDomainLanguage($this->domainLanguageRepository->find($id));

			$this->redirect('this');
		} catch(IOException $e) {
			$this->flashMessage('common.operation.error', FlashMessage::ERROR);
			$this->redirect('this');
		}
	}

}
