<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Forms;

use Voonne\Voonne\Forms\Controls\Button;
use Voonne\Voonne\Forms\Controls\Checkbox;
use Voonne\Voonne\Forms\Controls\CheckboxList;
use Voonne\Voonne\Forms\Controls\HiddenField;
use Voonne\Voonne\Forms\Controls\ImageButton;
use Voonne\Voonne\Forms\Controls\MultiSelectBox;
use Voonne\Voonne\Forms\Controls\RadioList;
use Voonne\Voonne\Forms\Controls\SelectBox;
use Voonne\Voonne\Forms\Controls\SubmitButton;
use Voonne\Voonne\Forms\Controls\TextArea;
use Voonne\Voonne\Forms\Controls\TextInput;
use Voonne\Voonne\Forms\Controls\UploadControl;


class Form extends \Nette\Application\UI\Form
{

	/**
	 * Adds single-line text input control to the form.
	 *
	 * @param string $name
	 * @param string $label
	 * @param int $cols
	 * @param int $maxLength
	 *
	 * @return TextInput
	 */
	public function addText($name, $label = null, $cols = null, $maxLength = null)
	{
		$control = new TextInput($label, $maxLength);
		$control->setAttribute('size', $cols);
		return $this[$name] = $control;
	}


	/**
	 * Adds single-line text input control used for sensitive input such as passwords.
	 *
	 * @param string $name
	 * @param string $label
	 * @param int $cols
	 * @param int $maxLength
	 *
	 * @return TextInput
	 */
	public function addPassword($name, $label = null, $cols = null, $maxLength = null)
	{
		$control = new TextInput($label, $maxLength);
		$control->setAttribute('size', $cols);
		return $this[$name] = $control->setType('password');
	}


	/**
	 * Adds multi-line text input control to the form.
	 *
	 * @param string $name
	 * @param string $label
	 * @param int $cols
	 * @param int $rows
	 *
	 * @return TextArea
	 */
	public function addTextArea($name, $label = null, $cols = null, $rows = null)
	{
		$control = new TextArea($label);
		$control->setAttribute('cols', $cols)->setAttribute('rows', $rows);
		return $this[$name] = $control;
	}


	/**
	 * Adds control that allows the user to upload files.
	 *
	 * @param string $name
	 * @param string $label
	 * @param bool $multiple
	 *
	 * @return UploadControl
	 */
	public function addUpload($name, $label = null, $multiple = false)
	{
		return $this[$name] = new UploadControl($label, $multiple);
	}


	/**
	 * Adds control that allows the user to upload multiple files.
	 *
	 * @param string $name
	 * @param string $label
	 *
	 * @return UploadControl
	 */
	public function addMultiUpload($name, $label = null)
	{
		return $this[$name] = new UploadControl($label, true);
	}


	/**
	 * Adds hidden form control used to store a non-displayed value.
	 *
	 * @param string $name
	 * @param mixed $default
	 *
	 * @return HiddenField
	 */
	public function addHidden($name, $default = null)
	{
		$control = new HiddenField();
		$control->setDefaultValue($default);
		return $this[$name] = $control;
	}


	/**
	 * Adds check box control to the form.
	 *
	 * @param string $name
	 * @param string $caption
	 *
	 * @return Checkbox
	 */
	public function addCheckbox($name, $caption = null)
	{
		return $this[$name] = new Checkbox($caption);
	}


	/**
	 * Adds set of radio button controls to the form.
	 *
	 * @param string $name
	 * @param string $label
	 * @param array $items
	 *
	 * @return RadioList
	 */
	public function addRadioList($name, $label = null, array $items = null)
	{
		return $this[$name] = new RadioList($label, $items);
	}


	/**
	 * Adds set of checkbox controls to the form.
	 *
	 * @param string $name
	 * @param string $label
	 * @param array $items
	 *
	 * @return CheckboxList
	 */
	public function addCheckboxList($name, $label = null, array $items = null)
	{
		return $this[$name] = new CheckboxList($label, $items);
	}


	/**
	 * Adds select box control that allows single item selection.
	 *
	 * @param string $name
	 * @param string $label
	 * @param array $items
	 * @param int $size
	 *
	 * @return SelectBox
	 */
	public function addSelect($name, $label = null, array $items = null, $size = null)
	{
		$control = new SelectBox($label, $items);
		if ($size > 1) {
			$control->setAttribute('size', (int)$size);
		}
		return $this[$name] = $control;
	}


	/**
	 * Adds select box control that allows multiple item selection.
	 *
	 * @param string $name
	 * @param string $label
	 * @param array $items
	 * @param int $size
	 *
	 * @return MultiSelectBox
	 */
	public function addMultiSelect($name, $label = null, array $items = null, $size = null)
	{
		$control = new MultiSelectBox($label, $items);
		if ($size > 1) {
			$control->setAttribute('size', (int)$size);
		}
		return $this[$name] = $control;
	}


	/**
	 * Adds button used to submit form.
	 *
	 * @param string $name
	 * @param string $caption
	 *
	 * @return SubmitButton
	 */
	public function addSubmit($name, $caption = null)
	{
		return $this[$name] = new SubmitButton($caption);
	}


	/**
	 * Adds push buttons with no default behavior.
	 *
	 * @param string $name
	 * @param string $caption
	 *
	 * @return Button
	 */
	public function addButton($name, $caption = null)
	{
		return $this[$name] = new Button($caption);
	}


	/**
	 * Adds graphical button used to submit form.
	 *
	 * @param string $name
	 * @param string $src
	 * @param string $alt
	 *
	 * @return ImageButton
	 */
	public function addImage($name, $src = null, $alt = null)
	{
		return $this[$name] = new ImageButton($src, $alt);
	}


	/**
	 * Adds naming container to the form.
	 *
	 * @param string $name
	 * @param string|null $label
	 *
	 * @return Container
	 */
	public function addContainer($name, $label = null)
	{
		$control = new Container($label);
		$control->currentGroup = $this->currentGroup;
		return $this[$name] = $control;
	}

}
