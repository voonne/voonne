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
use Voonne\Voonne\Content\ContentForm;
use Voonne\Voonne\Controls\Control;
use Voonne\Voonne\InvalidStateException;
use Voonne\Voonne\Panels\Panel;
use Voonne\Voonne\Panels\Renderers\PanelRenderer;
use Voonne\Voonne\Panels\Renderers\RendererManager;


abstract class Layout extends Control
{

	const POSITION_TOP = 'TOP';
	const POSITION_BOTTOM = 'BOTTOM';
	const POSITION_LEFT = 'LEFT';
	const POSITION_RIGHT = 'RIGHT';
	const POSITION_CENTER = 'CENTER';

	/**
	 * @var RendererManager
	 */
	private $rendererManager;

	/**
	 * @var ContentForm
	 */
	private $contentForm;

	/**
	 * @var array
	 */
	private $panels = [];


	/**
	 * @return RendererManager
	 */
	public function getRendererManager()
	{
		return $this->rendererManager;
	}


	/**
	 * @return ContentForm
	 */
	public function getContentForm()
	{
		return $this->contentForm;
	}


	/**
	 * @return array
	 */
	public function getPanels()
	{
		return $this->panels;
	}


	/**
	 * @param RendererManager $rendererManager
	 * @param ContentForm $contentForm
	 * @param array $panels
	 */
	public function injectPrimary(RendererManager $rendererManager, ContentForm $contentForm, array $panels)
	{
		if($this->rendererManager !== null) {
			throw new InvalidStateException('Method ' . __METHOD__ . ' is intended for initialization and should not be called more than once.');
		}

		$this->rendererManager = $rendererManager;
		$this->contentForm = $contentForm;
		$this->panels = $panels;
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
