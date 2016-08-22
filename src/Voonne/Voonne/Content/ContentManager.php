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
use Voonne\Voonne\InvalidArgumentException;
use Voonne\Voonne\Panels\Panel;


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


	/**
	 * Adds panel.
	 *
	 * @param string $destination
	 * @param string $position
	 * @param Panel $panel
	 * @param integer $priority
	 *
	 * @throws InvalidArgumentException
	 */
	public function addPanel($destination, $position, Panel $panel, $priority = 100)
	{
		if(!in_array($position, [self::POSITION_TOP, self::POSITION_BOTTOM, self::POSITION_LEFT, self::POSITION_RIGHT, self::POSITION_CENTER])) {
			throw new InvalidArgumentException("Position must be '" . self::POSITION_TOP . "', '" . self::POSITION_BOTTOM . "', '" . self::POSITION_LEFT . "', '" . self::POSITION_RIGHT . "' or '" . self::POSITION_CENTER . "', '"  . $position . "' given.");
		}

		$this->panels[$destination][$position][$priority][] = $panel;
	}


	/**
	 * Returns array of corresponding panels for specific destination.
	 *
	 * @param string $destination
	 *
	 * @return array
	 */
	public function getPanels($destination)
	{
		$panels = [
			self::POSITION_TOP => [],
			self::POSITION_BOTTOM => [],
			self::POSITION_LEFT => [],
			self::POSITION_RIGHT => [],
			self::POSITION_CENTER => []
		];

		if(!empty($this->panels[$destination])) {
			foreach($this->panels[$destination] as $positionName => $position) {
				krsort($position);

				foreach ($position as $priority) {
					foreach ($priority as $panel) {
						$reflectionClass = new ReflectionClass($panel);

						$panels[$positionName][Strings::webalize($reflectionClass->getShortName())] = $panel;
					}
				}
			}
		}

		return $panels;
	}

}
