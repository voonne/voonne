<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Layouts\Layout21;

use Nette\Localization\ITranslator;
use Voonne\Voonne\Content\ContentManager;
use Voonne\Voonne\Layouts\Layout;


class Layout21 extends Layout
{

	/**
	 * @var ContentManager
	 */
	private $contentManager;

	/**
	 * @var array
	 */
	private $elements;


	public function __construct(ContentManager $contentManager, ITranslator $translator)
	{
		parent::__construct($translator);

		$this->contentManager = $contentManager;
	}


	public function beforeRender()
	{
		$this->elements = $this->contentManager->getPanels();

		foreach($this->elements[ContentManager::POSITION_LEFT] as $name => $panel) {
			$this->setupPanel($panel);

			$this->addComponent($this->getPanelRendererFactory()->create($panel), $name);
		}

		foreach($this->elements[ContentManager::POSITION_RIGHT] as $name => $panel) {
			$this->setupPanel($panel);

			$this->addComponent($this->getPanelRendererFactory()->create($panel), $name);
		}
	}


	public function render()
	{
		$this->template->setFile(__DIR__ . '/Layout21.latte');

		$this->template->elements = $this->elements;

		$this->template->render();
	}

}
