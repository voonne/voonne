<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Panels\Renderers\BasicPanelRenderer;

use Voonne\Voonne\Panels\BasicPanel;
use Voonne\Voonne\Panels\Renderers\PanelRenderer;


class BasicPanelRenderer extends PanelRenderer
{

	/**
	 * @var BasicPanel
	 */
	private $panel;


	public function __construct(BasicPanel $basicPanel)
	{
		parent::__construct();

		$this->panel = $basicPanel;
	}


	public function beforeRender()
	{
		$this->addComponent($this->panel, 'panel');
	}


	public function render()
	{
		$this->template->setFile(__DIR__ . '/BasicPanelRenderer.latte');

		$this->template->panel = $this->panel;

		$this->template->render();
	}

}
