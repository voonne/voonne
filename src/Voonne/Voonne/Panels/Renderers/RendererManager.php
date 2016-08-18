<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Panels\Renderers;

use Nette\DI\Container;
use ReflectionClass;
use Voonne\Voonne\NotRegisteredException;
use Voonne\Voonne\Panels\Panel;


class RendererManager
{

	const TAG_RENDERER = 'voonne.panel.renderer';

	/**
	 * @var Container
	 */
	private $container;


	public function __construct(Container $container)
	{
		$this->container = $container;
	}


	/**
	 * Returns renderer corresponding to the specified type panel.
	 *
	 * @param Panel $panel
	 *
	 * @return PanelRenderer
	 *
	 * @throws NotRegisteredException
	 */
	public function getRenderer(Panel $panel)
	{
		foreach($this->getRenderers() as $type => $renderer) {
			if($panel instanceof $type) {
				return $renderer;
			}
		}

		throw new NotRegisteredException("Renderer panel for '" . get_class($panel) . "' not found.");
	}


	/**
	 * Return list of registrated renderers.
	 *
	 * @return array
	 */
	private function getRenderers()
	{
		$renderers = [];

		foreach($this->container->findByTag(self::TAG_RENDERER) as $name => $attribute) {
			$rendererFactory = $this->container->getService($name);
			$renderer = $rendererFactory->create();

			$reflectionClass = new ReflectionClass(get_class($renderer));

			$parameters = $reflectionClass->getMethod('render')->getParameters();

			if(isset($parameters[0])) {
				$renderers[$parameters[0]->getClass()->getName()] = $renderer;
			}
		}

		return $renderers;
	}

}
