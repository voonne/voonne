<?php

namespace Voonne\TestVoonne\Layouts;

use Codeception\Test\Unit;
use Mockery;
use Mockery\MockInterface;
use UnitTester;
use Voonne\UsersModule\Panels\PanelManager;
use Voonne\Voonne\Content\ContentForm;
use Voonne\Voonne\InvalidStateException;
use Voonne\Voonne\Layouts\Layout;
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
	private $panelManager;

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
		$this->panelManager = Mockery::mock(PanelManager::class);
		$this->contentForm = Mockery::mock(ContentForm::class);

		$this->layout = new TestLayout();
		$this->layout->injectPrimary($this->rendererManager, $this->panelManager, $this->contentForm);
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testInitialize()
	{
		$this->assertEquals($this->rendererManager, $this->layout->getRendererManager());
		$this->assertEquals($this->contentForm, $this->layout->getContentForm());

		$this->expectException(InvalidStateException::class);
		$this->layout->injectPrimary($this->rendererManager, $this->panelManager, $this->contentForm);
	}

}


class TestLayout extends Layout
{

}
