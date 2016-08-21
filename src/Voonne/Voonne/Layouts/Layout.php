<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Layouts;

use Nette\Application\UI\ITemplateFactory;
use Voonne\Voonne\Content\ContentForm;
use Voonne\Voonne\Controls\Control;
use Voonne\Voonne\InvalidStateException;
use Voonne\Voonne\Panels\Panel;
use Voonne\Voonne\Panels\Renderers\PanelRenderer\PanelRendererFactory;


abstract class Layout extends Control
{

	/**
	 * @var ITemplateFactory
	 */
	private $templateFactory;

	/**
	 * @var ContentForm
	 */
	private $contentForm;

	/**
	 * @var PanelRendererFactory
	 */
	private $panelRendererFactory;


	/**
	 * @return ITemplateFactory
	 */
	public function getTemplateFactory()
	{
		return $this->templateFactory;
	}


	/**
	 * @return ContentForm
	 */
	public function getContentForm()
	{
		return $this->contentForm;
	}


	/**
	 * @return PanelRendererFactory
	 */
	public function getPanelRendererFactory()
	{
		return $this->panelRendererFactory;
	}


	/**
	 * @param ITemplateFactory $templateFactory
	 * @param ContentForm $contentForm
	 * @param PanelRendererFactory $panelRendererFactory
	 */
	public function injectPrimary(ITemplateFactory $templateFactory, ContentForm $contentForm, PanelRendererFactory $panelRendererFactory)
	{
		if($this->templateFactory !== null) {
			throw new InvalidStateException('Method ' . __METHOD__ . ' is intended for initialization and should not be called more than once.');
		}

		$this->templateFactory = $templateFactory;
		$this->contentForm = $contentForm;
		$this->panelRendererFactory = $panelRendererFactory;
	}


	/**
	 * @param Panel $panel
	 */
	public function setupPanel(Panel $panel)
	{
		$panel->setTemplateFactory($this->getTemplateFactory());
		$panel->setupPanel();
		$panel->setupForm($this->getContentForm());
	}

}
