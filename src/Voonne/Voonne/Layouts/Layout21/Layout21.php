<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Layouts\Layout21;

use Voonne\Voonne\Layouts\Layout;


class Layout21 extends Layout
{

	/**
	 * @var array
	 */
	private $panels;


	public function beforeRender()
	{
		parent::beforeRender();

		$this->panels = $this->getPanels();

		foreach($this->panels[self::POSITION_LEFT] as $name => $panel) {
			$rendererFactory = $this->getRendererManager()->getRendererFactory($panel);

			$this->addComponent($rendererFactory->create($panel), $name);
		}

		foreach($this->panels[self::POSITION_RIGHT] as $name => $panel) {
			$rendererFactory = $this->getRendererManager()->getRendererFactory($panel);

			$this->addComponent($rendererFactory->create($panel), $name);
		}
	}


	public function render()
	{
		$this->template->setFile(__DIR__ . '/Layout21.latte');

		$this->template->panels = $this->panels;

		$this->template->render();
	}

}
