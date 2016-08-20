<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Controls\Breadcrumbs;

use Voonne\Voonne\Controls\Control;
use Voonne\Voonne\Pages\Group;
use Voonne\Voonne\Pages\Page;


class BreadcrumbsControl extends Control
{

	public function render(Page $page)
	{
		$this->template->setFile(__DIR__ . '/BreadcrumbsControl.latte');

		$this->template->cascade = $this->getCascade($page);

		$this->template->render();
	}


	/**
	 * @param Page $page
	 *
	 * @return array
	 */
	private function getCascade(Page $page)
	{
		$cascade = [];
		$current = $page;

		do {
			$cascade[] = $current;

			$current = $current->getParent();
		} while(!($current instanceof Group));
		$cascade[] = $current;

		return array_reverse($cascade);
	}

}
