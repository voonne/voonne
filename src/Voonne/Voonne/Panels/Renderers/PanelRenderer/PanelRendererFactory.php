<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Panels\Renderers\PanelRenderer;

use Nette\Localization\ITranslator;
use Voonne\Voonne\Panels\Panel;
use Voonne\Voonne\Panels\Renderers\RendererManager;


class PanelRendererFactory
{

	/**
	 * @var RendererManager
	 */
	private $rendererManager;

	/**
	 * @var ITranslator
	 */
	private $translator;


	public function __construct(RendererManager $rendererManager, ITranslator $translator)
	{
		$this->rendererManager = $rendererManager;
		$this->translator = $translator;
	}


	/**
	 * @param Panel $panel
	 *
	 * @return PanelRenderer
	 */
	public function create(Panel $panel)
	{
		return new PanelRenderer($panel, $this->rendererManager, $this->translator);
	}

}
