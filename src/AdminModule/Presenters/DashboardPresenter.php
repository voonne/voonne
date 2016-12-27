<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\AdminModule\Presenters;

use Voonne\Widgets\WidgetManager;


class DashboardPresenter extends BaseAuthorizedPresenter
{

	/**
	 * @var WidgetManager
	 * @inject
	 */
	public $widgetManager;


	public function renderDefault()
	{
		foreach ($this->widgetManager->getWidgets() as $name => $widget) {
			$this->addComponent($widget, $name);
		}

		$this->template->widgets = $this->getWidgets();
	}


	private function getWidgets()
	{
		$widgets = [[], []];
		$i = 0;

		foreach ($this->widgetManager->getWidgets() as $name => $widget) {
			if ($i % 2 == 0) {
				$widgets[0][$name] = $widget;
			} else {
				$widgets[1][$name] = $widget;
			}

			$i++;
		}

		return $widgets;
	}

}
