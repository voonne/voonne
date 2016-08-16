<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Panels\BasicPanelTest;

use Voonne\Voonne\Panels\BasicPanel;


class BasicPanelTest extends BasicPanel
{

	public function setupPanel()
	{
		$this->setTitle('Basic panel test');
	}


	public function render()
	{
		$this->template->setFile(__DIR__ . '/BasicPanelTest.latte');

		$this->template->render();
	}

}
