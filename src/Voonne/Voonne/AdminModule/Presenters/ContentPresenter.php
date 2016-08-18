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
use Voonne\Voonne\Forms\Form;
use Voonne\Voonne\Layouts\Layout21\Layout21;
use Voonne\Voonne\Layouts\LayoutManager;


class ContentPresenter extends BaseAuthorizedPresenter
{

	/**
	 * @var ContentManager
	 * @inject
	 */
	public $contentManager;

	/**
	 * @var LayoutManager
	 * @inject
	 */
	public $layoutManager;

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


	protected function createComponentLayout()
	{
		$layout = $this->layoutManager->getLayout(Layout21::class);

		$layout->injectPrimary(
			$this->container->getService('voonne.templateFactory'),
			$this->contentForm);

		return $layout;
	}

}
