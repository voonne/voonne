<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file LICENCE.md that was distributed with this source code.
 */

namespace Voonne\Voonne\DI;

use Nette\Application\IPresenterFactory;
use Nette\Application\IRouter;
use Nette\Application\Routers\RouteList;
use Nette\DI\CompilerExtension;
use Voonne\Voonne\Routers\RouterFactory;


class VoonneExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		/* router */

		$builder->addDefinition('voonne.router')
			->setFactory(RouterFactory::class . '::createRouter')
			->setAutowired(false);
	}


	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		/* router and mapping */

		$builder->getDefinition($builder->getByType(IPresenterFactory::class))
			->addSetup('setMapping', [['Admin' => 'Voonne\Voonne\AdminModule\*Module\Presenters\*Presenter']]);

		// inspired by: https://github.com/kdyby/console/blob/master/src/Kdyby/Console/DI/ConsoleExtension.php
		$routerServiceName = $builder->getByType(IRouter::class) ? : 'router';
		$builder->addDefinition('voonne.originalRouter', $builder->getDefinition($routerServiceName))
			->setAutowired(false);

		$builder->removeDefinition($routerServiceName);

		$builder->addDefinition($routerServiceName)
			->setClass(RouteList::class)
			->addSetup('offsetSet', [NULL, '@voonne.router'])
			->addSetup('offsetSet', [NULL, '@voonne.originalRouter']);
	}

}
