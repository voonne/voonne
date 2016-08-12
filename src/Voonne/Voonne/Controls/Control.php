<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Controls;

use Nette\ComponentModel\IContainer;
use Nette\Localization\ITranslator;


abstract class Control extends \Nette\Application\UI\Control
{

	/**
	 * @var ITranslator
	 */
	protected $translator;


	public function __construct(ITranslator $translator, IContainer $parent = null, $name = null)
	{
		parent::__construct($parent, $name);

		$this->translator = $translator;
	}

}
