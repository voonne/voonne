<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Layouts\Layout1;

use Voonne\Voonne\Layouts\Layout;


class Layout1 extends Layout
{

	/**
	 * @var array
	 */
	private $panelsCenter = [];


	public function beforeRender()
	{
		parent::beforeRender();

		$this->panelsCenter = $this->getPanelManager()->getByTag(self::POSITION_CENTER);

		foreach($this->panelsCenter as $name => $panel) {
			$rendererFactory = $this->getRendererManager()->getRendererFactory($panel);

			$this->addComponent($rendererFactory->create($panel), $name);
		}
	}


	public function render()
	{
		$this->template->setFile(__DIR__ . '/Layout1.latte');

		$this->template->panelsCenter = $this->panelsCenter;

		$this->template->render();
	}

}
