<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Layouts;

use Nette\DI\Container;
use Nette\SmartObject;
use Voonne\Voonne\NotRegisteredException;


class LayoutManager
{

	use SmartObject;

	const TAG_LAYOUT = 'voonne.layout';

	/**
	 * @var Container
	 */
	private $container;


	public function __construct(Container $container)
	{
		$this->container = $container;
	}


	/**
	 * Returns corresponding layout.
	 *
	 * @param $layoutName
	 *
	 * @return Layout
	 *
	 * @throws NotRegisteredException
	 */
	public function getLayout($layoutName)
	{
		foreach($this->getLayouts() as $name => $layout) {
			if(is_a($layout, $layoutName)) {
				return $layout;
			}
		}

		throw new NotRegisteredException("Layout with name '" . $layoutName . "' not found.");
	}


	/**
	 * @return array
	 */
	private function getLayouts()
	{
		$layouts = [];

		foreach($this->container->findByTag(self::TAG_LAYOUT) as $name => $attribute) {
			$layoutFactory = $this->container->getService($name);
			$layout = $layoutFactory->create();
			$layouts[] = $layout;
		}

		return $layouts;
	}

}
