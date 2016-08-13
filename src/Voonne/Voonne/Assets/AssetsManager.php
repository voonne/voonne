<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Assets;

use Defr\MimeType;
use Voonne\Voonne\DirectoryNotFoundException;
use Voonne\Voonne\FileNotFoundException;
use Voonne\Voonne\InvalidArgumentException;


class AssetsManager
{

	/**
	 * @var Resource[]
	 */
	public $assets  = [];

	/**
	 * @var array
	 */
	public $scripts  = [];

	/**
	 * @var array
	 */
	public $styles  = [];


	/**
	 * Adds asset with unique name.
	 *
	 * @param string $name
	 * @param string $path
	 */
	public function addAsset($name, $path)
	{
		if(file_exists($path)) {
			$this->assets[$name] = new Resource($name, file_get_contents($path), MimeType::get($path));
		} else {
			throw new FileNotFoundException("File '$path' doesn't exist.");
		}
	}


	/**
	 * Adds script to a specific location.
	 *
	 * @param string $name
	 * @param string $path
	 */
	public function addScript($name, $path)
	{
		if(file_exists($path)) {
			$this->scripts[$name][] = realpath($path);
		} else {
			throw new FileNotFoundException("File '$path' doesn't exist.");
		}
	}


	/**
	 * Adds style to a specific location.
	 *
	 * @param string $name
	 * @param string $path
	 */
	public function addStyle($name, $path)
	{
		if(file_exists($path)) {
			$this->styles[$name][] = realpath($path);
		} else {
			throw new FileNotFoundException("File '$path' doesn't exist.");
		}
	}


	private function getScript($name)
	{
		if(!isset($this->scripts[$name])) {
			throw new InvalidArgumentException("Script with name '$name' does not exists.");
		}

		$content = '';

		foreach($this->scripts[$name] as $script) {
			$content .= file_get_contents($script) . PHP_EOL;
		}

		return new Resource('scripts/' . $name . '.js', $content, 'application/javascript');
	}


	private function getStyle($name)
	{
		if(!isset($this->styles[$name])) {
			throw new InvalidArgumentException("Style with name '$name' does not exists.");
		}

		$content = '';

		foreach($this->styles[$name] as $style) {
			$content .= file_get_contents($style) . PHP_EOL;
		}

		return new Resource('styles/' . $name . '.css', $content, 'text/css');
	}


	/**
	 * Returns the resource by name if exits.
	 *
	 * @param $name
	 *
	 * @return \Voonne\Voonne\Assets\Resource
	 *
	 * @throws FileNotFoundException
	 * @throws InvalidArgumentException
	 * @throws DirectoryNotFoundException
	 */
	public function getResource($name)
	{
		try {
			$path = explode('/', $name);
			if(count($path) == 2) {
				switch ($path[0]) {
					case 'scripts':
						return $this->getScript(pathinfo($name, PATHINFO_FILENAME));
						break;
					case 'styles':
						return $this->getStyle(pathinfo($name, PATHINFO_FILENAME));
						break;
				}
			}
		} catch(InvalidArgumentException $e) {}

		if(isset($this->assets[$name])) {
			return $this->assets[$name];
		} else {
			throw new FileNotFoundException('The requested resource does not exist.');
		}
	}

}

function realpath($path)
{
	return $path;
}
