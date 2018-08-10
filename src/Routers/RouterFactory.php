<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Routers;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Http\Request;


class RouterFactory
{

	public static function createRouter(Request $request, $prefix)
	{
		$router = new RouteList;

		$router[] = $frontRouter = new RouteList('Admin');

		$frontRouter[] = new Route(trim($prefix), 'Default:default');

		$frontRouter[] = new Route(
			sprintf('%s%slost-password', trim($prefix), !empty($prefix) ? '/' : ''),
			'Default:lostPassword'
		);

		$frontRouter[] = new Route(
			sprintf('%s%snew-password/<code>', trim($prefix), !empty($prefix) ? '/' : ''),
			'Default:newPassword'
		);

		$frontRouter[] = new Route(
			sprintf('%s%sdashboard', trim($prefix), !empty($prefix) ? '/' : ''),
			'Dashboard:default'
		);

		$frontRouter[] = new ContentRoute($request, $prefix);

		$frontRouter[] = $apiRouter = new RouteList('Api');

		$apiRouter[] = new Route(
			sprintf('%s%sapi/v1/assets/<name .+>', trim($prefix), !empty($prefix) ? '/' : ''),
			'Assets:default'
		);

		$apiRouter[] = new Route(
			sprintf('%s%sapi/v1/files/<directoryName>/<fileName .+>', trim($prefix), !empty($prefix) ? '/' : ''),
			'Files:default'
		);

		return $router;
	}

}
