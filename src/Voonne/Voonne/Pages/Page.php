<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Pages;

use Nette\Utils\Strings;
use ReflectionClass;
use Voonne\Voonne\Content\ContentForm;
use Voonne\Voonne\Controls\Control;
use Voonne\Voonne\DuplicateEntryException;
use Voonne\Voonne\InvalidArgumentException;
use Voonne\Voonne\InvalidStateException;
use Voonne\Voonne\Layouts\Layout;
use Voonne\Voonne\Layouts\Layout1\Layout1;
use Voonne\Voonne\Layouts\LayoutManager;
use Voonne\Voonne\Panels\Panel;
use Voonne\Voonne\Panels\Renderers\RendererManager;


abstract class Page extends Control
{

	/**
	 * @var LayoutManager
	 */
	private $layoutManager;

	/**
	 * @var RendererManager
	 */
	private $rendererManager;

	/**
	 * @var ContentForm
	 */
	private $contentForm;

	/**
	 * @var string
	 */
	private $pageName;

	/**
	 * @var string
	 */
	private $pageTitle;

	/**
	 * @var bool
	 */
	private $visible = true;

	/**
	 * @var string
	 */
	private $layout = Layout1::class;

	/**
	 * @var array
	 */
	private $panels = [];


	public function __construct($pageName, $pageTitle)
	{
		parent::__construct();

		$this->pageName = $pageName;
		$this->pageTitle = $pageTitle;
	}


	/**
	 * @return string
	 */
	public function getPageName()
	{
		return $this->pageName;
	}


	/**
	 * @return string
	 */
	public function getPageTitle()
	{
		return $this->pageTitle;
	}


	/**
	 * @return bool
	 */
	public function isVisible()
	{
		return $this->visible;
	}


	/**
	 * Sets as visible.
	 */
	public function show()
	{
		$this->visible = true;
	}


	/**
	 * Sets as hide.
	 */
	public function hide()
	{
		$this->visible = false;
	}


	/**
	 * @param string $layout
	 */
	public function setLayout($layout)
	{
		if(!is_subclass_of($layout, Layout::class)) {
			throw new InvalidArgumentException("Layout class must be child of '" . Layout::class . "', '"  . $layout . "' given.");
		}

		$this->layout = $layout;
	}


	/**
	 * Adds a panel to the page.
	 *
	 * @param Panel $panel
	 * @param int $position
	 * @param int $priority
	 *
	 * @throws InvalidArgumentException
	 */
	public function addPanel(Panel $panel, $position, $priority = 100)
	{
		if (!in_array($position, [Layout::POSITION_TOP, Layout::POSITION_BOTTOM, Layout::POSITION_LEFT, Layout::POSITION_RIGHT, Layout::POSITION_CENTER])) {
			throw new InvalidArgumentException("Position must be '" . Layout::POSITION_TOP . "', '" . Layout::POSITION_BOTTOM . "', '" . Layout::POSITION_LEFT . "', '" . Layout::POSITION_RIGHT . "' or '" . Layout::POSITION_CENTER . "', '"  . $position . "' given.");
		}

		foreach ($this->getPanels() as $position1) {
			foreach ($position1 as $panel1) {
				if($panel instanceof $panel1) {
					throw new DuplicateEntryException("Panel named '" . get_class($panel) . "' is already exists.");
				}
			}
		}

		$reflectionClass = new ReflectionClass($panel);

		$this->panels[$position][$priority][Strings::webalize($reflectionClass->getShortName())] = $panel;
	}


	/**
	 * @return array
	 */
	public function getPanels()
	{
		$panels = [
			Layout::POSITION_TOP => [],
			Layout::POSITION_BOTTOM => [],
			Layout::POSITION_LEFT => [],
			Layout::POSITION_RIGHT => [],
			Layout::POSITION_CENTER => []
		];

		foreach ($this->panels as $positionName => $position) {
			krsort($position);

			foreach ($position as $priority) {
				foreach ($priority as $panelName => $panel) {
					$panels[$positionName][$panelName] = $panel;
				}
			}
		}

		return $panels;
	}


	/**
	 * @param LayoutManager $layoutManager
	 * @param RendererManager $rendererManager
	 * @param ContentForm $contentForm
	 */
	public function injectPrimary(LayoutManager $layoutManager, RendererManager $rendererManager, ContentForm $contentForm)
	{
		if($this->layoutManager !== null) {
			throw new InvalidStateException('Method ' . __METHOD__ . ' is intended for initialization and should not be called more than once.');
		}

		$this->layoutManager = $layoutManager;
		$this->rendererManager = $rendererManager;
		$this->contentForm = $contentForm;
	}


	public function beforeRender()
	{
		$layout = $this->layoutManager->getLayout($this->layout);

		$layout->injectPrimary(
			$this->rendererManager,
			$this->contentForm,
			$this->getPanels()
		);

		$this->addComponent($layout, 'layout');
	}


	public function render()
	{
		$this->template->setFile(__DIR__ . '/Page.latte');

		$this->template->render();
	}

}
