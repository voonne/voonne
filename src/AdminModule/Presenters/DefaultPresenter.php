<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\AdminModule\Presenters;

use Voonne\Messages\FlashMessage;
use Voonne\Model\IOException;
use Voonne\Voonne\AdminModule\Forms\LostPasswordFormFactory;
use Voonne\Voonne\AdminModule\Forms\NewPasswordFormFactory;
use Voonne\Voonne\AdminModule\Forms\SignInFormFactory;
use Voonne\Voonne\Model\Entities\LostPassword;
use Voonne\Voonne\Model\Repositories\LostPasswordRepository;


class DefaultPresenter extends BasePresenter
{

	/**
	 * @var LostPasswordRepository
	 * @inject
	 */
	public $lostPasswordRepository;

	/**
	 * @var LostPassword
	 */
	private $lostPassword;

	/**
	 * @var string
	 * @persistent
	 */
	public $code;


	protected function startup()
	{
		parent::startup();

		if ($this->user->isLoggedIn()) {
			$this->redirect('Dashboard:default');
		}
	}


	public function actionNewPassword($code)
	{
		try {
			$this->lostPassword = $this->lostPasswordRepository->findOneBy(['code' => $code]);
		} catch (IOException $e) {
			$this->flashMessage('voonne-signIn.newPassword.notFound', FlashMessage::ERROR);
			$this->redirect('Default:default');
		}
	}


	protected function createComponentSignInForm(SignInFormFactory $factory)
	{
		$form = $factory->create();

		$factory->onSuccess[] = function() {
			$this->flashMessage('voonne-common.authentication.signedIn', FlashMessage::INFO);
			$this->redirect('Dashboard:default');
		};

		return $form;
	}


	protected function createComponentLostPasswordForm(LostPasswordFormFactory $factory)
	{
		$form = $factory->create();

		$factory->onSuccess[] = function() {
			$this->flashMessage('voonne-signIn.lostPassword.emailSend', FlashMessage::SUCCESS);
			$this->redirect('this');
		};

		return $form;
	}


	protected function createComponentNewPasswordForm(NewPasswordFormFactory $factory)
	{
		$form = $factory->create($this->lostPassword);

		$factory->onSuccess[] = function() {
			$this->flashMessage('voonne-signIn.newPassword.completed', FlashMessage::SUCCESS);
			$this->redirect('Default:default', ['code' => null]);
		};

		return $form;
	}

}
