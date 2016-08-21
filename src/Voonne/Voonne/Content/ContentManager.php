<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Content;

use Nette\Utils\Strings;
use ReflectionClass;


class ContentManager
{

	const POSITION_TOP = 'TOP';
	const POSITION_BOTTOM = 'BOTTOM';
	const POSITION_LEFT = 'LEFT';
	const POSITION_RIGHT = 'RIGHT';
	const POSITION_CENTER = 'CENTER';

	/**
	 * @var array
	 */
	private $panels = [];


	public function addPanel($element, $position, $priority = 100)
	{
		$this->panels[$position][$priority][] = $element;
	}


	public function getPanels()
	{
		$panels = [
			self::POSITION_TOP => [],
			self::POSITION_BOTTOM => [],
			self::POSITION_LEFT => [],
			self::POSITION_RIGHT => [],
			self::POSITION_CENTER => []
		];

		if(!empty($this->panels)) {
			foreach($this->panels as $positionName => $position) {
				krsort($position);

				foreach ($position as $priority) {
					foreach ($priority as $factory) {
						$panel = $factory->create();
						$reflectionClass = new ReflectionClass($panel);

						$panels[$positionName][Strings::webalize($reflectionClass->getShortName())] = $panel;
					}
				}
			}
		}

		return $panels;
	}

}
