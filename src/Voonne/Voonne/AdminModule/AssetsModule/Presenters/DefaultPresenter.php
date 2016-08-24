<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\AdminModule\AssetsModule\Presenters;

use Nette\Application\Responses\TextResponse;
use Nette\Http\Response;
use Voonne\Voonne\Assets\AssetsManager;
use Voonne\Voonne\FileNotFoundException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Presenter;


class DefaultPresenter extends Presenter
{

	/**
	 * @var Response
	 * @inject
	 */
	public $response;

	/**
	 * @var AssetsManager
	 * @inject
	 */
	public $assetsManager;


	public function renderDefault($name)
	{
		try {
			$resource = $this->assetsManager->getResource($name);

			$this->response->setContentType($resource->getMimeType());
			$this->sendResponse(new TextResponse($resource->getContent()));
		} catch (FileNotFoundException $e) {
			throw new BadRequestException('Not found', 404);
		}
	}

}
