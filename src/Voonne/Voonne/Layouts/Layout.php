<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Layouts;

use Nette\ComponentModel\IComponent;
use Voonne\UsersModule\Panels\PanelManager;
use Voonne\Voonne\Content\ContentForm;
use Voonne\Voonne\Controls\Control;
use Voonne\Voonne\InvalidStateException;
use Voonne\Voonne\Panels\Panel;
use Voonne\Voonne\Panels\Renderers\PanelRenderer;
use Voonne\Voonne\Panels\Renderers\RendererManager;


abstract class Layout extends Control
{

	const POSITION_TOP = 'top';
	const POSITION_BOTTOM = 'bottom';
	const POSITION_LEFT = 'left';
	const POSITION_RIGHT = 'right';
	const POSITION_CENTER = 'center';

	/**
	 * @var RendererManager
	 */
	private $rendererManager;

	/**
	 * @var PanelManager
	 */
	private $panelManager;

	/**
	 * @var ContentForm
	 */
	private $contentForm;


	/**
	 * @return RendererManager
	 */
	public function getRendererManager()
	{
		return $this->rendererManager;
	}


	/**
	 * @return PanelManager
	 */
	public function getPanelManager()
	{
		return $this->panelManager;
	}


	/**
	 * @return ContentForm
	 */
	public function getContentForm()
	{
		return $this->contentForm;
	}


	/**
	 * @param RendererManager $rendererManager
	 * @param PanelManager $panelManager
	 * @param ContentForm $contentForm
	 */
	public function injectPrimary(RendererManager $rendererManager, PanelManager $panelManager, ContentForm $contentForm)
	{
		if($this->rendererManager !== null) {
			throw new InvalidStateException('Method ' . __METHOD__ . ' is intended for initialization and should not be called more than once.');
		}

		$this->rendererManager = $rendererManager;
		$this->panelManager = $panelManager;
		$this->contentForm = $contentForm;
	}


	/**
	 * @param IComponent $component
	 * @param string $name
	 * @param string|null $insertBefore
	 *
	 * @return $this
	 */
	public function addComponent(IComponent $component, $name, $insertBefore = null)
	{
		if ($component instanceof PanelRenderer) {
			// INJECT
			$component->injectPrimary($this->getContentForm());
		}

		parent::addComponent($component, $name, $insertBefore);

		return $this;
	}

}
