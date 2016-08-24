<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\AdminModule\Presenters;

use Voonne\Voonne\AdminModule\Forms\SignInFormFactory;
use Voonne\Voonne\AuthenticationException;
use Voonne\Voonne\Forms\Form;
use Voonne\Voonne\Messages\FlashMessage;


class DefaultPresenter extends BasePresenter
{

	protected function startup()
	{
		parent::startup();

		if ($this->user->isLoggedIn()) {
			$this->redirect('Dashboard:default');
		}
	}


	protected function createComponentSignInForm(SignInFormFactory $factory)
	{
		$form = $factory->create();

		$form->onSuccess[] = [$this, 'signInSuccess'];

		return $form;
	}


	public function signInSuccess(Form $form, $values)
	{
		try {
			if ($values->stayLoggedIn) {
				$this->getUser()->setExpiration('14 days', false);
			} else {
				$this->getUser()->setExpiration('20 minutes', true);
			}

			$this->authenticator->authenticate($values->email, $values->password);

			$this->flashMessage('voonne-common.authentication.signedIn', FlashMessage::INFO);
			$this->redirect('Dashboard:default');
		} catch(AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}

}
