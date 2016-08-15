<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Content\Latte;

use Voonne\Voonne\Content\ContentForm;


class Engine extends \Latte\Engine
{

	/**
	 * @var ContentForm
	 */
	private $contentForm;


	public function __construct(ContentForm $contentForm)
	{
		parent::__construct();

		$this->contentForm = $contentForm;
	}


	public function createTemplate($name, array $params = [])
	{
		$template = parent::createTemplate($name, $params);

		$template->global->formsStack[] = $this->contentForm;

		return $template;
	}

}
