<?php

namespace Voonne\TestVoonne\Pages;

use Codeception\Test\Unit;
use Mockery;
use Mockery\MockInterface;
use UnitTester;
use Voonne\Voonne\Content\ContentForm;
use Voonne\Voonne\DuplicateEntryException;
use Voonne\Voonne\InvalidArgumentException;
use Voonne\Voonne\InvalidStateException;
use Voonne\Voonne\Layouts\Layout;
use Voonne\Voonne\Layouts\Layout1\Layout1;
use Voonne\Voonne\Layouts\LayoutManager;
use Voonne\Voonne\Pages\Page;
use Voonne\Voonne\Panels\Panel;
use Voonne\Voonne\Panels\Renderers\RendererManager;


class PageTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var MockInterface
	 */
	private $layoutManager;

	/**
	 * @var MockInterface
	 */
	private $rendererManager;

	/**
	 * @var MockInterface
	 */
	private $contentForm;

	/**
	 * @var Page
	 */
	private $page;


	protected function _before()
	{
		$this->layoutManager = Mockery::mock(LayoutManager::class);
		$this->rendererManager = Mockery::mock(RendererManager::class);
		$this->contentForm = Mockery::mock(ContentForm::class);

		$this->page = new TestPage('name', 'title');
		$this->page->injectPrimary($this->layoutManager, $this->rendererManager, $this->contentForm);
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testInitialize()
	{
		$this->assertEquals('name', $this->page->getPageName());
		$this->assertEquals('title', $this->page->getPageTitle());
		$this->assertTrue($this->page->isVisibleInMenu());

		$this->expectException(InvalidStateException::class);
		$this->page->injectPrimary($this->layoutManager, $this->rendererManager, $this->contentForm);
	}


	public function testVisibility()
	{
		$this->page->hideFromMenu();

		$this->assertFalse($this->page->isVisibleInMenu());

		$this->page->showInMenu();

		$this->assertTrue($this->page->isVisibleInMenu());
	}


	public function testAddPanel()
	{
		$panel = Mockery::mock(Panel::class);

		$this->page->addPanel($panel, [Layout::POSITION_CENTER]);

		$this->expectException(DuplicateEntryException::class);
		$this->page->addPanel($panel, [Layout::POSITION_CENTER]);

		$this->expectException(DuplicateEntryException::class);
		$this->page->addPanel($panel, [Layout::POSITION_TOP]);

		$this->expectException(InvalidArgumentException::class);
		$this->page->addPanel($panel, ['BAD_POSITION']);
	}


	public function testBeforeRender()
	{
		$layout = Mockery::mock(Layout1::class);

		$this->layoutManager->shouldReceive('getLayout')
			->once()
			->with(Layout1::class)
			->andReturn($layout);

		$layout->shouldReceive('injectPrimary')
			->once()
			->withAnyArgs();

		$layout->shouldReceive('setParent')
			->once()
			->with($this->page, 'layout');

		$layout->shouldReceive('startup')
			->once()
			->withNoArgs();

		$layout->shouldReceive('beforeRender')
			->once()
			->withNoArgs();

		$this->page->beforeRender();
	}

}


class TestPage extends Page
{

}
