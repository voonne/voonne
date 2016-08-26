<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Routers;

use Nette\Application\Request;
use Nette\Application\Routers\Route;
use Nette\Http\Url;


class ContentRoute extends Route
{

	/**
	 * @var \Nette\Http\Request
	 */
	private $request;


	public function __construct(\Nette\Http\Request $request)
	{
		parent::__construct('admin/<groupName>/<pageName>/', 'Content:default', 0);

		$this->request = $request;
	}


	/**
	 * @param Request $appRequest
	 * @param Url $refUrl
	 *
	 * @return string|null
	 */
	public function constructUrl(Request $appRequest, Url $refUrl)
	{
		if($appRequest->getPresenterName() != 'Content') {
			return null;
		}

		$parameters = $appRequest->getParameters();

		if(!isset($parameters['groupName']) || !isset($parameters['pageName'])) {
			return null;
		}

		$url = $refUrl->getBasePath() . 'admin/' . $parameters['groupName'] . '/' . $parameters['pageName']. '/';

		unset($parameters['groupName'], $parameters['pageName'], $parameters['action']);

		// when is current url same as constructed, then add current parameters
		if($url == $this->request->getUrl()->getPath()) {
			$parameters = array_merge($refUrl->getQueryParameters(), $parameters);

			if(isset($refUrl->getQueryParameters()['do'])) {
				unset($parameters['do']);
			}

			if(isset($refUrl->getQueryParameters()['_fid'])) {
				unset($parameters['_fid']);
			}

			foreach ($parameters as $key => $value) { // remove components parameters
				if(strpos($key, '-') !== false && isset($this->request->getUrl()->getQueryParameters()[$key])) {
					unset($parameters[$key]);
				}
			}
		}

		$query = http_build_query($parameters, '', '&');
		if ($query !== '') {
			$url .= '?' . $query;
		}

		return $url;
	}

}
