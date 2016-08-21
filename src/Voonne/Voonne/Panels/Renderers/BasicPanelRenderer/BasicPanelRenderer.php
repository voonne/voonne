<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Panels\Renderers\BasicPanelRenderer;

use Nette\Localization\ITranslator;
use Voonne\Voonne\Controls\Control;
use Voonne\Voonne\Panels\BasicPanel;


class BasicPanelRenderer extends Control
{

	/**
	 * @var BasicPanel
	 */
	private $basicPanel;


	public function __construct(BasicPanel $basicPanel, ITranslator $translator)
	{
		parent::__construct($translator);

		$this->basicPanel = $basicPanel;
	}


	public function beforeRender()
	{
		$this->addComponent($this->basicPanel, 'panel');
	}


	public function render()
	{
		$this->template->setFile(__DIR__ . '/BasicPanelRenderer.latte');

		$this->template->panel = $this->basicPanel;

		$this->template->render();
	}

}
