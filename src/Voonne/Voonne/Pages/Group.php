<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Pages;


class Group
{

	/**
	 * @var string
	 */
	private $label;

	/**
	 * @var string|null
	 */
	private $icon;


	public function __construct($label, $icon = null)
	{
		$this->label = $label;
		$this->icon = $icon;
	}


	/**
	 * @return string
	 */
	public function getLabel()
	{
		return $this->label;
	}


	/**
	 * @return null|string
	 */
	public function getIcon()
	{
		return $this->icon;
	}

}
