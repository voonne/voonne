<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Panels\Renderers\BasicPanelRenderer;

use Voonne\Voonne\Controls\Control;
use Voonne\Voonne\Panels\BasicPanel;


class BasicPanelRenderer extends Control
{

	public function render(BasicPanel $panel)
	{
		$this->template->setFile(__DIR__ . '/BasicPanelRenderer.latte');

		$this->template->panel = $this['panel'] = $panel;

		$this->template->render();
	}

}
