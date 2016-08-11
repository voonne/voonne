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


class DefaultPresenter extends BasePresenter
{

	protected function createComponentSignInForm(SignInFormFactory $factory)
	{
		return $factory->create();
	}

}
