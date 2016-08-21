<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Controls;

use Kdyby\Autowired\AutowireComponentFactories;
use Kdyby\Autowired\AutowireProperties;
use Nette\Application\AbortException;
use Nette\ComponentModel\IComponent;
use Nette\ComponentModel\IContainer;
use Nette\Localization\ITranslator;
use stdClass;


abstract class Control extends \Nette\Application\UI\Control
{

	use AutowireProperties;
	use AutowireComponentFactories;

	/**
	 * @var ITranslator
	 */
	protected $translator;


	public function __construct(ITranslator $translator, IContainer $parent = null, $name = null)
	{
		parent::__construct($parent, $name);

		$this->translator = $translator;
	}


	/**
	 * Common render method.
	 *
	 * @return void
	 */
	public function beforeRender()
	{

	}


	/**
	 * @param integer $code
	 * @param string $destination
	 * @param array|mixed $args
	 *
	 * @throws AbortException
	 */
	public function redirect($code, $destination = null, $args = [])
	{
		if(count(explode('.', $destination)) > 1) {
			$args['destination'] = $destination;

			$this->getPresenter()->redirect('Content:default', $args);
		} elseif($code == 'this') {
			parent::redirect('this');
		} else {
			parent::redirect($code, $destination, $args);
		}
	}


	/**
	 * @param string $destination
	 * @param array $args
	 *
	 * @return string
	 */
	public function link($destination, $args = [])
	{
		if(count(explode('.', $destination)) > 1) {
			$args['destination'] = $destination;

			return $this->getPresenter()->link('Content:default', $args);
		} else {
			return parent::link($destination, $args);
		}
	}


	/**
	 * @param string $message
	 * @param string $type
	 *
	 * @return stdClass
	 */
	public function message($message, $type = 'info')
	{
		return $this->presenter->flashMessage($message, $type);
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

		if($component instanceof Control) {
			$component->beforeRender();
		}

		return $this;
	}

}
