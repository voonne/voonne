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
use Voonne\Voonne\Controls\Control;


abstract class LayoutControl extends Control
{

	/**
	 * @var ITemplateFactory
	 */
	private $templateFactory;


	public function setTemplateFactory(ITemplateFactory $templateFactory)
	{
		$this->templateFactory = $templateFactory;
	}


	public function getTemplateFactory()
	{
		return $this->templateFactory;
	}

}
