<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Panels\Renderers\BasicPanelRenderer;

use Voonne\Voonne\Panels\BasicPanel;


class BasicPanelRendererFactory
{

	/**
	 * @param BasicPanel $panel
	 *
	 * @return BasicPanelRenderer
	 */
	public function create(BasicPanel $panel)
	{
		return new BasicPanelRenderer($panel);
	}

}
