<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\AdminModule\Presenters;

use Voonne\Voonne\Controls\DomainSelect\IDomainSelectControlFactory;
use Voonne\Voonne\Controls\Menu\IMenuControlFactory;
use Voonne\Voonne\Messages\FlashMessage;
use Voonne\Voonne\Security\User;


abstract class BaseAuthorizedPresenter extends BasePresenter
{

	/**
	 * @var User
	 * @inject
	 */
	public $securityUser;


	protected function startup()
	{
		parent::startup();

		if (!$this->user->isLoggedIn()) {
			$this->flashMessage('voonne-common.authentication.unauthenticatedAccess', FlashMessage::INFO);
			$this->redirect('Default:default');
		}

		$this->template->signedInUser = $this->securityUser->getUser();
	}


	public function handleSignOut()
	{
		$this->getUser()->logout(true);

		$this->flashMessage('voonne-common.authentication.signedOut', FlashMessage::INFO);
		$this->redirect('Default:default');
	}


	protected function createComponentMenuControl(IMenuControlFactory $factory)
	{
		return $factory->create();
	}


	protected function createComponentDomainSelectControl(IDomainSelectControlFactory $factory)
	{
		return $factory->create();
	}

}
