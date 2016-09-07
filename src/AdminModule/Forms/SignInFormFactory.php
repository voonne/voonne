<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\AdminModule\Forms;

use Voonne\Forms\FormFactory;


class SignInFormFactory extends FormFactory
{

	public function create()
	{
		$form = parent::create();

		$form->addText('email', 'voonne-signIn.signIn.email');

		$form->addPassword('password', 'voonne-signIn.signIn.password');

		$form->addCheckbox('stayLoggedIn', 'voonne-signIn.signIn.stayLoggedIn');

		$form->addSubmit('submit', 'voonne-signIn.signIn.submit');

		return $form;
	}

}
