<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Panels\Renderers\BlankPanelRenderer;

use Nette\Localization\ITranslator;
use Voonne\Voonne\Panels\BlankPanel;


class BlankPanelRendererFactory
{

	/**
	 * @var ITranslator
	 */
	private $translator;


	public function __construct(ITranslator $translator)
	{
		$this->translator = $translator;
	}


	/**
	 * @param BlankPanel $blankPanel
	 *
	 * @return BlankPanelRenderer
	 */
	public function create(BlankPanel $blankPanel)
	{
		return new BlankPanelRenderer($blankPanel, $this->translator);
	}

}
