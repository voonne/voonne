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
	 * @throws DuplicateEntryException
	 */
	public function addGroup($name, $label, $priority = 100, $icon = null)
	{
		if(isset($this->getGroups()[$name])) {
			throw new DuplicateEntryException("Group is named '$name' already exists.");
		}

		$this->groups[$priority][$name] = new Group($label, $icon);
	}


	/**
	 * Returns all registered groups.
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

}
