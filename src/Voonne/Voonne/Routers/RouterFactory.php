<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file LICENCE.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Routers;

use Nette\Application\IRouter;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Http\Request;


class RouterFactory
{

	/**
	 * @param Request $request
	 *
	 * @return IRouter
	 */
	public static function createRouter(Request $request)
	{
		$router = new RouteList;

		$router[] = $frontRouter = new RouteList('Admin');

		$frontRouter[] = new Route('admin/', 'Default:default');

		$frontRouter[] = $assetsRouter = new RouteList('Assets');

		$assetsRouter[] = new Route('admin/assets/<name .+>', 'Default:default');

		return $router;
	}

}
