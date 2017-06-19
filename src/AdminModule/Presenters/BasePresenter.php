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
use Nette\ComponentModel\IComponent;
use Voonne\Controls\Control;
use Voonne\Domains\DomainManager;
use Voonne\Security\Authenticator;
use Voonne\Voonne\Controls\FlashMessage\IFlashMessageControlFactory;
use Voonne\Voonne\Controls\FormError\IFormErrorControlFactory;


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


	/**
	 * @param string $destination
	 * @param array $args
	 *
	 * @return string
	 */
	public function link($destination, $args = [])
	{
		$parts = explode('.', $destination);

		if (count($parts) == 2) {
			$args['groupName'] = $parts[0];
			$args['pageName'] = $parts[1];

			return parent::link('Content:default', $args);
		} else {
			return parent::link($destination, $args);
		}
	}


	/**
	 * @param string|null $destination
	 * @param array $args
	 *
	 * @return bool
	 */
	public function isLinkCurrent($destination = null, $args = [])
	{
		$parts = explode('.', $destination);

		if (count($parts) == 2) {
			$args['groupName'] = $parts[0];
			$args['pageName'] = $parts[1];

			return parent::isLinkCurrent('Content:default', $args);
		} else {
			return parent::isLinkCurrent($destination, $args);
		}
	}


	/**
	 * @param IComponent $component
	 * @param string $name
	 * @param string|null $insertBefore
	 *
	 * @return $this
	 */
	public function addComponent(IComponent $component, $name, $insertBefore = null)
	{
		parent::addComponent($component, $name, $insertBefore);

		if ($component instanceof Control) {
			// STARTUP
			$component->startup();

			// BEFORE RENDER
			$component->beforeRender();
		}

		return $this;
	}


	public function injectSynchronize(DomainManager $domainManager)
	{
		$domainManager->synchronize();
	}

}
