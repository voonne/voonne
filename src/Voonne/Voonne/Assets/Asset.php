<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Assets;

use Nette\SmartObject;


class Asset
{

	use SmartObject;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $content;

	/**
	 * @var string
	 */
	private $mimeType;


	/**
	 * @param string $name
	 * @param string $content
	 * @param string $mimeType
	 */
	public function __construct($name, $content, $mimeType)
	{
		$this->name = $name;
		$this->content = $content;
		$this->mimeType = $mimeType;
	}


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}


	/**
	 * @return string
	 */
	public function getMimeType()
	{
		return $this->mimeType;
	}

}
