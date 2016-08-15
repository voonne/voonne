<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\AdminModule\Presenters;

use Nette\DI\Container;
use Voonne\Voonne\Content\ContentForm;
use Voonne\Voonne\Content\ContentManager;
use Voonne\Voonne\Layouts\Layout21\ILayout21ControlFactory;
use Voonne\Voonne\Forms\Form;


class ContentPresenter extends BaseAuthorizedPresenter
{

	/**
	 * @var ContentManager
	 * @inject
	 */
	public $contentManager;

	/**
	 * @var Container
	 * @inject
	 */
	public $container;

	/**
	 * @var ContentForm
	 * @inject
	 */
	public $contentForm;


	public function actionDefault()
	{
		$this['form'] = $this->contentForm;

		$this->contentForm->onSuccess[] = [$this, 'success'];
	}


	public function success(Form $form, $values)
	{
		\Tracy\Debugger::barDump($values);
	}


	protected function createComponentLayout(ILayout21ControlFactory $factory)
	{
		$layout = $factory->create();

		$layout->setTemplateFactory($this->container->getService('voonne.templateFactory'));

		return $layout;
	}

}
