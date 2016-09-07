<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Controls\FlashMessage;

use Voonne\Controls\Control;


class FlashMessageControl extends Control
{

	public function render($flashes)
	{
		$this->template->setFile(__DIR__ . '/FlashMessageControl.latte');

		$this->template->flashes = $flashes;

		$this->template->render();
	}

}
