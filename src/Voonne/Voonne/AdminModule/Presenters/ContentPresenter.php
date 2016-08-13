<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\AdminModule\Presenters;

use Voonne\Voonne\Content\ContentManager;
use Voonne\Voonne\Controls\Layout21\ILayout21ControlFactory;
use Voonne\Voonne\Forms\Form;


class ContentPresenter extends BaseAuthorizedPresenter
{

	/**
	 * @var ContentManager
	 * @inject
	 */
	public $contentManager;

	private $form;


	public function actionDefault()
	{
		$this['form'] = $this->form = new Form();

		$this->form->setTranslator($this->translator);

		$this->form->onSuccess[] = [$this, 'success'];
	}


	public function success(Form $form, $values)
	{
		\Tracy\Debugger::barDump($values);
	}


	protected function createComponentLayout(ILayout21ControlFactory $factory)
	{
		return $factory->create();
	}

}
