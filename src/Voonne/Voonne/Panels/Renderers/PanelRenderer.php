<?php
/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Panels\Renderers;

use Nette\ComponentModel\IComponent;
use Voonne\Voonne\Content\ContentForm;
use Voonne\Voonne\Controls\Control;
use Voonne\Voonne\InvalidStateException;
use Voonne\Voonne\Panels\Panel;


abstract class PanelRenderer extends Control
{

	/**
	 * @var ContentForm
	 */
	private $contentForm;


	/**
	 * @return ContentForm
	 */
	public function getContentForm()
	{
		return $this->contentForm;
	}


	/**
	 * @param ContentForm $contentForm
	 */
	public function injectPrimary(ContentForm $contentForm)
	{
		if($this->contentForm !== null) {
			throw new InvalidStateException('Method ' . __METHOD__ . ' is intended for initialization and should not be called more than once.');
		}

		$this->contentForm = $contentForm;
	}


	/**
	 * @param IComponent $component
	 * @param string $name
	 * @param string|null $insertBefore
	 *
	 * @return $this
	 */
	public function addComponent(IComponent $component, $name, $insertBefore = null)
	{
		parent::addComponent($component, $name, $insertBefore);

		if ($component instanceof Panel) {
			// FORM SETUP
			$component->setupForm($this->getContentForm());
		}

		return $this;
	}

}
