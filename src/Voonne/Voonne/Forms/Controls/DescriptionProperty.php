<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file LICENCE.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Forms\Controls;


trait DescriptionProperty
{

	/**
	 * @var string|null
	 */
	private $description;


	/**
	 * @param string $description
	 *
	 * @return $this
	 */
	public function setDescription($description)
	{
		$this->description = $description;

		return $this;
	}


	/**
	 * @return string|null
	 */
	public function getDescription()
	{
		return $this->description;
	}

}
