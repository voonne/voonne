<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Pages;

use Voonne\Voonne\DuplicateEntryException;


class Group
{

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var string|null
	 */
	private $icon;

	/**
	 * @var array
	 */
	private $pages = [];


	public function __construct($name, $title, $icon = null)
	{
		$this->name = $name;
		$this->title = $title;
		$this->icon = $icon;
	}


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}


	/**
	 * @return null|string
	 */
	public function getIcon()
	{
		return $this->icon;
	}


	/**
	 * Adds a child item.
	 *
	 * @param Page $page
	 * @param integer $priority
	 *
	 * @throws DuplicateEntryException
	 */
	public function addPage(Page $page, $priority = 100)
	{
		if(isset($this->getPages()[$page->getName()])) {
			throw new DuplicateEntryException("Page with name '" . $page->getName() . "' already exists.");
		}

		$this->pages[$priority][$page->getName()] = $page;

		$page->setParent($this);
	}


	/**
	 * Returns all children.
	 *
	 * @return array
	 */
	public function getPages()
	{
		$pages = [];

		krsort($this->pages);

		foreach($this->pages as $priority) {
			foreach($priority as $name => $page) {
				$pages[$name] = $page;
			}
		}

		return $pages;
	}


	/**
	 * Returns complete structure of group.
	 *
	 * @return array
	 */
	public function getStructure()
	{
		$recursive = function($pages) use (&$recursive) {
			$result = [];

			foreach($pages as $page) {
				$result[$page->getPath()] = $page;

				if(!empty($page->getPages())) {
					$result = array_merge($result, $recursive($page->getPages()));
				}
			}

			return $result;
		};

		return $recursive($this->getPages());
	}

}
