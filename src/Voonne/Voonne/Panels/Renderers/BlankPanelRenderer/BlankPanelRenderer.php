<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Panels\Renderers\BlankPanelRenderer;

use Nette\Localization\ITranslator;
use Voonne\Voonne\Controls\Control;
use Voonne\Voonne\Panels\BlankPanel;


class BlankPanelRenderer extends Control
{

	/**
	 * @var BlankPanel
	 */
	private $blankPanel;


	public function __construct(BlankPanel $panel, ITranslator $translator)
	{
		parent::__construct($translator);

		$this->blankPanel = $panel;
	}


	public function beforeRender()
	{
		$this->addComponent($this->blankPanel, 'panel');
	}


	public function render()
	{
		$this->template->setFile(__DIR__ . '/BlankPanelRenderer.latte');

		$this->template->panel = $this->blankPanel;

		$this->template->render();
	}

}
