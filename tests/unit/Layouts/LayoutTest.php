<?php

namespace Voonne\Voonne\Layouts;

use Codeception\Test\Unit;
use Mockery;
use Mockery\MockInterface;
use UnitTester;
use Voonne\Voonne\Content\ContentForm;
use Voonne\Voonne\InvalidStateException;
use Voonne\Voonne\Panels\Renderers\RendererManager;


class LayoutTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var MockInterface
	 */
	private $rendererManager;

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
		$this->rendererManager = Mockery::mock(RendererManager::class);
		$this->contentForm = Mockery::mock(ContentForm::class);

		$this->layout = new TestLayout();
		$this->layout->injectPrimary($this->rendererManager, $this->contentForm, [
			Layout::POSITION_TOP => [],
			Layout::POSITION_BOTTOM => [],
			Layout::POSITION_LEFT => [],
			Layout::POSITION_RIGHT => [],
			Layout::POSITION_CENTER => []
		]);
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testInitialize()
	{
		$this->assertEquals($this->rendererManager, $this->layout->getRendererManager());
		$this->assertEquals($this->contentForm, $this->layout->getContentForm());
		$this->assertEquals([
			Layout::POSITION_TOP => [],
			Layout::POSITION_BOTTOM => [],
			Layout::POSITION_LEFT => [],
			Layout::POSITION_RIGHT => [],
			Layout::POSITION_CENTER => []
		], $this->layout->getPanels());

		$this->expectException(InvalidStateException::class);
		$this->layout->injectPrimary($this->rendererManager, $this->contentForm, [
			Layout::POSITION_TOP => [],
			Layout::POSITION_BOTTOM => [],
			Layout::POSITION_LEFT => [],
			Layout::POSITION_RIGHT => [],
			Layout::POSITION_CENTER => []
		]);
	}

}


class TestLayout extends Layout
{

}
