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
use Voonne\Layouts\LayoutManager;
use Voonne\Pages\Page;
use Voonne\Pages\PageManager;
use Voonne\Panels\Renderers\RendererManager;
use Voonne\Voonne\Content\ContentForm;
use Voonne\Voonne\Controls\FormError\IFormErrorControlFactory;


class ContentPresenter extends BaseAuthorizedPresenter
{

	/**
	 * @var PageManager
	 * @inject
	 */
	public $pageManager;

	/**
	 * @var LayoutManager
	 * @inject
	 */
	public $layoutManager;

	/**
	 * @var RendererManager
	 * @inject
	 */
	public $rendererManager;

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
	public $groupName;

	/**
	 * @var string
	 * @persistent
	 */
	public $pageName;


	public function actionDefault()
	{
		$groups = $this->pageManager->getGroups();

		$groupName = lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $this->groupName))));
		$pageName = lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $this->pageName))));

		if (!isset($groups[$groupName]) || !isset($groups[$groupName]->getPages()[$pageName])) {
			throw new BadRequestException('Not found', 404);
		}

		$this->template->page = $this->page = $groups[$groupName]->getPages()[$pageName];

		$this->page->injectPrimary(
			$this->layoutManager,
			$this->rendererManager,
			$this->contentForm
		);

		$this->addComponent($this->page, 'page');

		/* content form */

		$this->addComponent($this->contentForm, 'form');
	}


	protected function createComponentFormError(IFormErrorControlFactory $factory)
	{
		return $factory->create();
	}

}
