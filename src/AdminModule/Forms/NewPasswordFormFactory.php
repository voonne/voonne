<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\AdminModule\Forms;

use Nette\Localization\ITranslator;
use Voonne\Forms\Form;
use Voonne\Forms\FormFactory;
use Voonne\Voonne\Model\Entities\LostPassword;
use Voonne\Voonne\Model\Facades\LostPasswordFacade;
use Voonne\Voonne\Model\Facades\UserFacade;


class NewPasswordFormFactory extends FormFactory
{

	/**
	 * @var LostPasswordFacade
	 */
	private $lostPasswordFacade;

	/**
	 * @var UserFacade
	 */
	private $userFacade;

	/**
	 * @var LostPassword
	 */
	private $lostPassword;

	/**
	 * @var array
	 */
	public $onSuccess = [];


	public function __construct(ITranslator $translator, LostPasswordFacade $lostPasswordFacade, UserFacade $userFacade)
	{
		parent::__construct($translator);

		$this->lostPasswordFacade = $lostPasswordFacade;
		$this->userFacade = $userFacade;
	}


	public function create(LostPassword $lostPassword)
	{
		$this->lostPassword  = $lostPassword;

		$form = $this->getForm();

		$form->addPassword('password', 'voonne-signIn.newPassword.password')
			->setRequired('voonne-signIn.newPassword.passwordFill')
			->addRule(Form::PATTERN, 'voonne-signIn.newPassword.passwordFormat', '^.*(?=.{8,})(?=.*[a-z])(?=.*[A-Z])(?=.*[\d\W]).*$');

		$form->addSubmit('submit', 'voonne-signIn.newPassword.submit');

		$form->onSuccess[] = [$this, 'success'];

		return $form;
	}


	public function success(Form $form, $values)
	{
		$user = $this->lostPassword->getUser();

		$user->changePassword($values->password);

		$this->lostPasswordFacade->remove($this->lostPassword);
		$this->userFacade->save($user);

		$this->onSuccess();
	}

}
