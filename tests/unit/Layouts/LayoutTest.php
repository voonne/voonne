<?php

namespace Voonne\Voonne\Layouts;

use Codeception\Test\Unit;
use Mockery;
use Mockery\MockInterface;
use Nette\Application\UI\ITemplateFactory;
use Nette\Localization\ITranslator;
use UnitTester;
use Voonne\Voonne\Content\ContentForm;
use Voonne\Voonne\InvalidStateException;
use Voonne\Voonne\Panels\BasicPanel;


class LayoutTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var MockInterface
	 */
	private $translator;

	/**
	 * @var MockInterface
	 */
	private $templateFactory;

	/**
	 * @var MockInterface
	 */
	private $contentForm;

	/**
	 * @var Layout
	 */
	private $layout;


	protected function _before()
	{
		$this->translator = Mockery::mock(ITranslator::class);
		$this->templateFactory = Mockery::mock(ITemplateFactory::class);
		$this->contentForm = Mockery::mock(ContentForm::class);

		$this->layout = new TestLayout($this->translator);
		$this->layout->injectPrimary($this->templateFactory, $this->contentForm);
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testInitialize()
	{
		$this->assertEquals($this->templateFactory, $this->layout->getTemplateFactory());
		$this->assertEquals($this->contentForm, $this->layout->getContentForm());

		$this->expectException(InvalidStateException::class);
		$this->layout->injectPrimary($this->templateFactory, $this->contentForm);
	}


	public function testSetupPanel()
	{
		$panel = Mockery::mock(BasicPanel::class);

		$panel->shouldReceive('setTemplateFactory')
			->once()
			->with($this->templateFactory);

		$panel->shouldReceive('setupPanel')
			->once()
			->withNoArgs();

		$panel->shouldReceive('setupForm')
			->once()
			->with($this->contentForm);

		$this->layout->setupPanel($panel);
	}

}

class TestLayout extends Layout
{

}
