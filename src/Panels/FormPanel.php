<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Panels;

use Nette\Forms\Container;
use Nette\Forms\Controls\BaseControl;
use Voonne\Forms\Form;
use Voonne\Voonne\Content\ContentForm;
use Voonne\Voonne\InvalidArgumentException;
use Voonne\Voonne\InvalidStateException;


abstract class FormPanel extends Panel
{

	/**
	 * @var string
	 */
	private $title;


	/**
	 * @var Container
	 */
	private $formScope;


	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}


	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}


	/**
	 * @param ContentForm $contentForm
	 */
	public function injectPrimary(ContentForm $contentForm)
	{
		if($this->formScope !== null) {
			throw new InvalidStateException('Method ' . __METHOD__ . ' is intended for initialization and should not be called more than once.');
		}

		$this->formScope = $contentForm;
	}


	/**
	 * @param Container $formScope
	 */
	public function setFormScope(Container $formScope)
	{
		$form = $formScope;

		while(!empty($form->getParent())) {
			$form = $form->getParent();
		}

		if(!($form instanceof Form)) {
			throw new InvalidArgumentException("Form scope must be child of '" . Form::class . "'.");
		}

		$this->formScope = $formScope;
	}


	public function render()
	{
		$this->template->setFile(__DIR__ . '/FormPanel.latte');

		$this->template->formScope = $this->formScope;

		$this->template->render();
	}


	public function getFullName(BaseControl $component)
	{
		$recursive = function($component) use (&$recursive) {
			$result = [];

			if(!($component instanceof Form)) {
				$result[] = $component->name;

				$result = array_merge($recursive($component->getParent()), $result);
			}

			return $result;
		};

		return implode('-', $recursive($component));
	}

}
