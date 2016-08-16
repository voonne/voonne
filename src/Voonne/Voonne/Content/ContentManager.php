<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Content;

use Nette\Localization\ITranslator;


class ContentManager
{
	/**
	 * @var ITranslator
	 */
	private $translator;

	/**
	 * @var array
	 */
	private $panels = [];

	const POSITION_TOP = 'TOP';
	const POSITION_BOTTOM = 'BOTTOM';
	const POSITION_LEFT = 'LEFT';
	const POSITION_RIGHT = 'RIGHT';
	const POSITION_CENTER = 'CENTER';


	public function __construct(ITranslator $translator)
	{
		$this->translator = $translator;
	}


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
						$panels[$positionName][] = $factory->create();
					}
				}
			}

			return $panels;
		} else {
			return $panels;
		}
	}

}
