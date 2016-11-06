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
use Voonne\Model\IOException;
use Voonne\Voonne\DuplicateEntryException;
use Voonne\Voonne\Model\Entities\LostPassword;
use Voonne\Voonne\Model\Entities\User;
use Voonne\Voonne\Model\Facades\LostPasswordFacade;
use Voonne\Voonne\Model\Repositories\UserRepository;


class LostPasswordFormFactory extends FormFactory
{

	/**
	 * @var UserRepository
	 */
	private $userRepository;

	/**
	 * @var LostPasswordFacade
	 */
	private $lostPasswordFacade;

	/**
	 * @var array
	 */
	public $onSuccess = [];


	public function __construct(
		ITranslator $translator,
		UserRepository $userRepository,
		LostPasswordFacade $lostPasswordFacade
	)
	{
		parent::__construct($translator);

		$this->userRepository = $userRepository;
		$this->lostPasswordFacade = $lostPasswordFacade;
	}


	public function create()
	{
		$form = $this->getForm();

		$form->addText('email', 'voonne-signIn.lostPassword.email')
			->setAttribute('type', 'email')
			->setRequired('voonne-signIn.lostPassword.emailFill');

		$form->addSubmit('submit', 'voonne-signIn.lostPassword.submit');

		$form->onSuccess[] = [$this, 'success'];

		return $form;
	}


	public function success(Form $form, $values)
	{
		try {
			/** @var User $user */
			$user = $this->userRepository->findOneBy(['email' => $values->email]);

			$lostPassword = new LostPassword($user);

			$this->lostPasswordFacade->save($lostPassword);

			$this->onSuccess($lostPassword);
		} catch (IOException $e) {
			$form->addError('voonne-signIn.lostPassword.userNotFound');
		} catch (DuplicateEntryException $e) {
			$form->addError('voonne-common.operation.error');
		}
	}

}
