<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Content;

use Nette\Localization\ITranslator;
use Voonne\Forms\Form;


class ContentForm extends Form
{

	public function __construct(ITranslator $translator)
	{
		parent::__construct();

		$this->getElementPrototype()->setAttribute('novalidate', 'novalidate');
		$this->setTranslator($translator);
	}

}
