<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\AdminModule\ApiModule\Presenters;

use Nette\Application\BadRequestException;
use Nette\Application\Responses\TextResponse;
use Nette\Application\UI\Presenter;
use Nette\Http\Response;
use Voonne\Storage\FileNotFoundException;
use Voonne\Storage\StorageManager;


class FilesPresenter extends Presenter
{

	/**
	 * @var Response
	 * @inject
	 */
	public $response;

	/**
	 * @var StorageManager
	 * @inject
	 */
	public $storageManager;


	public function actionDefault($directoryName, $fileName)
	{
		try {
			$directory = $this->storageManager->getDirectory($directoryName);
			$file = $directory->getFile($fileName);

			$this->response->setContentType($file->getMimeType());
			$this->sendResponse(new TextResponse(file_get_contents($file->getPath())));
		} catch (FileNotFoundException $e) {
			throw new BadRequestException('Not found', 404);
		}
	}

}
