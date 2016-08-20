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
use Voonne\Voonne\InvalidArgumentException;
use Voonne\Voonne\NotFoundException;


class PageManager
{

	/**
	 * @var array
	 */
	private $groups = [];


	/**
	 * Adds new a group.
	 *
	 * @param string $name
	 * @param string $label
	 * @param string|null $icon
	 * @param integer|null $priority
	 *
	 * @return Group
	 *
	 * @throws DuplicateEntryException
	 */
	public function addGroup($name, $label, $priority = 100, $icon = null)
	{
		if(isset($this->getGroups()[$name])) {
			throw new DuplicateEntryException("Group is named '$name' already exists.");
		}

		return $this->groups[$priority][$name] = new Group($name, $label, $icon);
	}


	/**
	 * Adds new a page.
	 *
	 * @param string $groupName
	 * @param string $name
	 * @param Page $page
	 * @param integer $priority
	 *
	 * @throws InvalidArgumentException
	 */
	public function addPage($groupName, $name, Page $page, $priority = 100)
	{
		if(!isset($this->getGroups()[$groupName])) {
			throw new InvalidArgumentException("Group named '$groupName' does not exist.");
		}

		$current = $this->getGroups()[$groupName];

		$structure = explode('.', $name);

		for($i = 0; $i < count($structure); $i++) {
			if($i == (count($structure) - 1)) {
				$current->addPage($page, $priority);
			} else {
				$current = $current->getPages()[$structure[$i]];
			}
		}
	}


	/**
	 * Returns all groups.
	 *
	 * @return array
	 */
	public function getGroups()
	{
		$groups = [];

		krsort($this->groups);

		foreach($this->groups as $priority) {
			foreach($priority as $name => $group) {
				$groups[$name] = $group;
			}
		}

		return $groups;
	}


	/**
	 * Returns complete structure.
	 *
	 * @return array
	 */
	public function getStructure()
	{
		$pages = [];

		foreach($this->getGroups() as $group) {
			foreach($group->getStructure() as $path => $page) {
				$pages[$group->getName() . '.' . $path] = $page;
			}
		}

		return $pages;
	}


	/**
	 * Returns page by destination.
	 *
	 * @param $destination
	 *
	 * @return Page
	 *
	 * @throws NotFoundException
	 */
	public function findByDestination($destination)
	{
		$pages = $this->getStructure();

		if(isset($pages[$destination])) {
			return $pages[$destination];
		} else {
			throw new NotFoundException("Page '$destination' not found.");
		}
	}

}
