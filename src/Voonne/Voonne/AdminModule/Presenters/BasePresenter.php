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
use Nette\Application\UI\Presenter;
use Voonne\Voonne\Controls\FlashMessage\IFlashMessageControlFactory;
use Voonne\Voonne\Controls\FormError\IFormErrorControlFactory;
use Voonne\Voonne\Security\Authenticator;


abstract class BasePresenter extends Presenter
{

	use AutowireProperties;
	use AutowireComponentFactories;

	/**
	 * @var Authenticator
	 * @inject
	 */
	public $authenticator;

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


	protected function createComponentFlashMessageControl(IFlashMessageControlFactory $factory)
	{
		return $factory->create();
	}


	protected function createComponentFormErrorControl(IFormErrorControlFactory $factory)
	{
		return $factory->create();
	}

}
