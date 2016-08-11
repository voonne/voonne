<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\AdminModule\Presenters;

use Kdyby\Autowired\AutowireComponentFactories;
use Kdyby\Autowired\AutowireProperties;
use Kdyby\Translation\Translator;


abstract class Presenter extends \Nette\Application\UI\Presenter
{

	use AutowireProperties;
	use AutowireComponentFactories;

	/**
	 * @var Translator
	 * @inject
	 */
	public $translator;


	protected function startup()
	{
		parent::startup();

		$this->autoCanonicalize = false;

		$this->translator->setLocale('cs');
	}

}
