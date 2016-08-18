<?php

namespace Voonne\Voonne\Panels\Renderers;

use Codeception\Test\Unit;
use Mockery;
use Mockery\MockInterface;
use Nette\DI\Container;
use UnitTester;
use Voonne\Voonne\NotRegisteredException;
use Voonne\Voonne\Panels\BasicPanel;
use Voonne\Voonne\Panels\Renderers\BasicPanelRenderer\BasicPanelRenderer;
use Voonne\Voonne\Panels\Renderers\BasicPanelRenderer\IBasicPanelRendererFactory;
use Voonne\Voonne\Panels\Renderers\BlankPanelRenderer\BlankPanelRenderer;
use Voonne\Voonne\Panels\Renderers\BlankPanelRenderer\IBlankPanelRendererFactory;


class RendererManagerTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var MockInterface
	 */
	private $container;

	/**
	 * @var RendererManager
	 */
	private $rendererManager;


	protected function _before()
	{
		$this->container = Mockery::mock(Container::class);

		$this->rendererManager = new RendererManager($this->container);
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testGetExistingRenderer()
	{
		$basicPanelRendererFactory = Mockery::mock(IBasicPanelRendererFactory::class);
		$blankPanelRendererFactory = Mockery::mock(IBlankPanelRendererFactory::class);
		$basicPanelRenderer = Mockery::mock(BasicPanelRenderer::class);
		$blankPanelRenderer = Mockery::mock(BlankPanelRenderer::class);

		$panel = Mockery::mock(BasicPanel::class);

		$basicPanelRendererFactory->shouldReceive('create')
			->once()
			->withNoArgs()
			->andReturn($basicPanelRenderer);

		$blankPanelRendererFactory->shouldReceive('create')
			->once()
			->withNoArgs()
			->andReturn($blankPanelRenderer);

		$this->container->shouldReceive('findByTag')
			->once()
			->with(RendererManager::TAG_RENDERER)
			->andReturn(['voonne.basicPanelRenderer' => true, 'voonne.blankPanelRenderer' => true]);

		$this->container->shouldReceive('getService')
			->once()
			->with('voonne.basicPanelRenderer')
			->andReturn($basicPanelRendererFactory);

		$this->container->shouldReceive('getService')
			->once()
			->with('voonne.blankPanelRenderer')
			->andReturn($blankPanelRendererFactory);

		$this->assertEquals($basicPanelRenderer, $this->rendererManager->getRenderer($panel));
	}


	public function testGetNonexistentRenderer()
	{
		$blankPanelRendererFactory = Mockery::mock(IBlankPanelRendererFactory::class);
		$blankPanelRenderer = Mockery::mock(BlankPanelRenderer::class);

		$panel = Mockery::mock(BasicPanel::class);

		$blankPanelRendererFactory->shouldReceive('create')
			->once()
			->withNoArgs()
			->andReturn($blankPanelRenderer);

		$this->container->shouldReceive('findByTag')
			->once()
			->with(RendererManager::TAG_RENDERER)
			->andReturn(['voonne.blankPanelRenderer' => true]);

		$this->container->shouldReceive('getService')
			->once()
			->with('voonne.blankPanelRenderer')
			->andReturn($blankPanelRendererFactory);

		$this->expectException(NotRegisteredException::class);
		$this->rendererManager->getRenderer($panel);
	}

}
