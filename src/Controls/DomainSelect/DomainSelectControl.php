<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Controls\DomainSelect;

use Voonne\Controls\Control;
use Voonne\Messages\FlashMessage;
use Voonne\Model\IOException;
use Voonne\Security\User;
use Voonne\Voonne\Model\Repositories\DomainRepository;


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


	public function __construct(User $user, DomainRepository $domainRepository)
	{
		parent::__construct();

		$this->user = $user;
		$this->domainRepository = $domainRepository;
	}


	public function render()
	{
		$this->template->setFile(__DIR__ . '/DomainSelectControl.latte');

		$this->template->domains = $domains = $this->domainRepository->findAll();
		$this->template->currentDomain = $this->user->getCurrentDomain();

		$this->template->render();
	}


	public function handleDomain($id)
	{
		try {
			$this->user->setCurrentDomain($this->domainRepository->find($id));

			$this->redirect('this');
		} catch(IOException $e) {
			$this->flashMessage('common.operation.error', FlashMessage::ERROR);
			$this->redirect('this');
		}
	}

}
