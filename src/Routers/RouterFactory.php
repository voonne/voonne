<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Routers;

use Nette\Application\IRouter;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Http\Request;


class RouterFactory
{

	/**
	 * @return IRouter
	 */
	public static function createRouter(Request $request)
	{
		$router = new RouteList;

		$router[] = $frontRouter = new RouteList('Admin');

		$frontRouter[] = new Route('admin', 'Default:default');

		$frontRouter[] = new Route('admin/lost-password', 'Default:lostPassword');

		$frontRouter[] = new Route('admin/new-password/<code>', 'Default:newPassword');

		$frontRouter[] = new Route('admin/dashboard', 'Dashboard:default');

		$frontRouter[] = new ContentRoute($request);

		$frontRouter[] = $apiRouter = new RouteList('Api');

		$apiRouter[] = new Route('admin/api/v1/assets/<name .+>', 'Assets:default');

		$apiRouter[] = new Route('admin/api/v1/files/<directoryName>/<fileName .+>', 'Files:default');

		return $router;
	}

}
