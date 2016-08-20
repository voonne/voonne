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
use Voonne\Voonne\InvalidStateException;
use Voonne\Voonne\Layouts\Layout;
use Voonne\Voonne\Layouts\Layout1\Layout1;


class Page
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
	 * @var bool
	 */
	private $visible = true;

	/**
	 * @var Page|Group|null
	 */
	private $parent;

	/**
	 * @var string
	 */
	private $layout = Layout1::class;

	/**
	 * @var array
	 */
	private $pages = [];


	public function __construct($name, $title)
	{
		$this->name = $name;
		$this->title = $title;
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
	 * @return bool
	 */
	public function isVisible()
	{
		return $this->visible;
	}


	/**
	 * Returns the parent if any.
	 *
	 * @return Page|Group|null
	 */
	public function getParent()
	{
		return $this->parent;
	}


	/**
	 * @return string
	 */
	public function getLayout()
	{
		return $this->layout;
	}


	/**
	 * Sets as hide.
	 */
	public function hide()
	{
		$this->visible = false;
	}


	/**
	 * Sets as visible.
	 */
	public function show()
	{
		$this->visible = true;
	}


	/**
	 * @param string $layout
	 */
	public function setLayout($layout)
	{
		if(!is_subclass_of($layout, Layout::class)) {
			throw new InvalidArgumentException("Layout class must be child of '" . Layout::class . "', '"  . $layout . "' given.");
		}

		$this->layout = $layout;
	}


	/**
	 * Sets the parent of this items.
	 *
	 * @param Page|Group $parent
	 *
	 * @throws InvalidStateException
	 * @throws InvalidArgumentException
	 */
	public function setParent($parent)
	{
		if(!empty($this->parent)) {
			throw new InvalidStateException("Page '" . $this->getName() . "' already has a parent.");
		}

		if(!($parent instanceof Page || $parent instanceof Group)) {
			throw new InvalidArgumentException("Parent must be instance of " . Page::class . " or " . Group::class . ", " . gettype($parent) . " given.");
		}

		$this->parent = $parent;
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
	 * Returns full page path.
	 *
	 * @return string
	 */
	public function getPath()
	{
		$result = [];
		$current = $this;

		while(!($current instanceof Group)) {
			$result[] = $current->getName();

			$current = $current->getParent();
		}

		return implode('.', array_reverse($result));
	}

}
