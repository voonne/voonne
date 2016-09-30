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
use Nette\Security\User;
use Voonne\Forms\Form;
use Voonne\Forms\FormFactory;
use Voonne\Security\AuthenticationException;
use Voonne\Security\Authenticator;


class SignInFormFactory extends FormFactory
{

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var Authenticator
	 */
	private $authenticator;

	/**
	 * @var array
	 */
	public $onSuccess = [];


	public function __construct(ITranslator $translator, User $user, Authenticator $authenticator)
	{
		parent::__construct($translator);

		$this->user = $user;
		$this->authenticator = $authenticator;
	}


	public function create()
	{
		$form = $this->getForm();

		$form->addText('email', 'voonne-signIn.signIn.email')
			->setRequired('voonne-signIn.signIn.emailFill');

		$form->addPassword('password', 'voonne-signIn.signIn.password')
			->setRequired('voonne-signIn.signIn.passwordFill');

		$form->addCheckbox('stayLoggedIn', 'voonne-signIn.signIn.stayLoggedIn');

		$form->addSubmit('submit', 'voonne-signIn.signIn.submit');

		$form->onSuccess[] = [$this, 'success'];

		return $form;
	}


	public function success(Form $form, $values)
	{
		try {
			if ($values->stayLoggedIn) {
				$this->user->setExpiration('14 days', false);
			} else {
				$this->user->setExpiration('20 minutes', true);
			}

			$this->authenticator->authenticate($values->email, $values->password);

			$this->onSuccess();
		} catch(AuthenticationException $e) {
			$form->addError('voonne-common.authentication.wrongEmailOrPassword');
		}
	}

}
