<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\AdminModule\Presenters;

use Nette\Application\BadRequestException;
use Nette\DI\Container;
use Voonne\Voonne\Content\ContentForm;
use Voonne\Voonne\Content\ContentManager;
use Voonne\Voonne\Controls\Breadcrumbs\IBreadcrumbsControlFactory;
use Voonne\Voonne\Forms\Form;
use Voonne\Voonne\Layouts\Layout21\Layout21;
use Voonne\Voonne\Layouts\LayoutManager;
use Voonne\Voonne\NotFoundException;
use Voonne\Voonne\Pages\Page;
use Voonne\Voonne\Pages\PageManager;


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
	 * @var PageManager
	 * @inject
	 */
	public $pageManager;

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

	/**
	 * @var Page
	 */
	public $page;

	/**
	 * @var string
	 * @persistent
	 */
	public $destination;


	public function actionDefault()
	{
		try {
			$this->template->page = $this->page = $this->pageManager->findByDestination($this->destination);
		} catch(NotFoundException $e) {
			throw new BadRequestException('Not found', 404);
		}

		$this['form'] = $this->contentForm;

		$this->contentForm->onSuccess[] = [$this, 'success'];
	}


	public function success(Form $form, $values)
	{
		\Tracy\Debugger::barDump($values);
	}


	protected function createComponentLayout()
	{
		$layout = $this->layoutManager->getLayout($this->page->getLayout());

		$layout->injectPrimary(
			$this->container->getService('voonne.templateFactory'),
			$this->contentForm);

		return $layout;
	}


	protected function createComponentBreadcrumbsControl(IBreadcrumbsControlFactory $factory)
	{
		return $factory->create();
	}

}
