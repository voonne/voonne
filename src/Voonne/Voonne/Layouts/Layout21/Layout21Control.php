<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Controls\Layout21;

use Nette\Localization\ITranslator;
use Voonne\Voonne\Content\ContentManager;
use Voonne\Voonne\Controls\Control;


class Layout21Control extends Control
{

	/**
	 * @var ContentManager
	 */
	private $contentManager;


	public function __construct(ContentManager $contentManager, ITranslator $translator)
	{
		parent::__construct($translator);

		$this->contentManager = $contentManager;
	}


	public function render()
	{
		$this->template->setFile(__DIR__ . '/Layout21Control.latte');

		$this->template->elements = $elements = $this->contentManager->getElements();

		foreach($elements[ContentManager::POSITION_LEFT] as $index => $element) {
			$this['element_left_' . $index] = $element->create();
		}

		foreach($elements[ContentManager::POSITION_RIGHT] as $index => $element) {
			$this['element_right_' . $index] = $element->create();
		}

		$this->template->render();
	}

}
