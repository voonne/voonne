<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Controls\Menu;

use Voonne\Controls\Control;
use Voonne\Pages\PageManager;


class MenuControl extends Control
{

	/**
	 * @var PageManager
	 */
	private $pageManager;


	public function __construct(PageManager $pageManager)
	{
		parent::__construct();

		$this->pageManager = $pageManager;
	}


	public function render()
	{
		$this->template->setFile(__DIR__ . '/MenuControl.latte');

		$this->template->groups = $this->pageManager->getGroups();

		$this->template->render();
	}

}
