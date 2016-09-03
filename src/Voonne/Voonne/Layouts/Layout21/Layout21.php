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
	private $panelsLeft;

	/**
	 * @var array
	 */
	private $panelsRight;


	public function beforeRender()
	{
		parent::beforeRender();

		$this->panelsLeft = $this->getPanelManager()->getByTag(self::POSITION_LEFT);

		foreach($this->panelsLeft as $name => $panel) {
			$rendererFactory = $this->getRendererManager()->getRendererFactory($panel);

			$this->addComponent($rendererFactory->create($panel), $name);
		}

		$this->panelsRight = $this->getPanelManager()->getByTag(self::POSITION_RIGHT);

		foreach($this->panelsRight as $name => $panel) {
			$rendererFactory = $this->getRendererManager()->getRendererFactory($panel);

			$this->addComponent($rendererFactory->create($panel), $name);
		}
	}


	public function render()
	{
		$this->template->setFile(__DIR__ . '/Layout21.latte');

		$this->template->panelsLeft = $this->panelsLeft;
		$this->template->panelsRight = $this->panelsRight;

		$this->template->render();
	}

}
