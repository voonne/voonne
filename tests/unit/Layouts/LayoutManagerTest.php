<?php

namespace Voonne\TestVoonne\Layouts;

use Codeception\Test\Unit;
use Mockery;
use Mockery\MockInterface;
use Nette\DI\Container;
use UnitTester;
use Voonne\Voonne\Layouts\Layout1\ILayout1Factory;
use Voonne\Voonne\Layouts\Layout1\Layout1;
use Voonne\Voonne\Layouts\Layout21\ILayout21Factory;
use Voonne\Voonne\Layouts\Layout21\Layout21;
use Voonne\Voonne\Layouts\LayoutManager;
use Voonne\Voonne\NotRegisteredException;


class LayoutManagerTest extends Unit
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
	 * @var LayoutManager
	 */
	private $layoutManager;


	protected function _before()
	{
		$this->container = Mockery::mock(Container::class);

		$this->layoutManager = new LayoutManager($this->container);
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testGetExistingLayout()
	{
		$layout1Factory = Mockery::mock(ILayout1Factory::class);
		$layout21Factory = Mockery::mock(ILayout21Factory::class);
		$layout1 = Mockery::mock(Layout1::class);
		$layout21 = Mockery::mock(Layout21::class);

		$layout1Factory->shouldReceive('create')
			->once()
			->withNoArgs()
			->andReturn($layout1);

		$layout21Factory->shouldReceive('create')
			->once()
			->withNoArgs()
			->andReturn($layout21);

		$this->container->shouldReceive('findByTag')
			->once()
			->with(LayoutManager::TAG_LAYOUT)
			->andReturn(['voonne.layout1Factory' => true, 'voonne.layout21Factory' => true]);

		$this->container->shouldReceive('getService')
			->once()
			->with('voonne.layout1Factory')
			->andReturn($layout1Factory);

		$this->container->shouldReceive('getService')
			->once()
			->with('voonne.layout21Factory')
			->andReturn($layout21Factory);

		$this->layoutManager->getLayout(Layout1::class);
	}


	public function testGetNonexistentLayout()
	{
		$layout21Factory = Mockery::mock(ILayout21Factory::class);
		$layout21 = Mockery::mock(Layout21::class);

		$layout21Factory->shouldReceive('create')
			->once()
			->withNoArgs()
			->andReturn($layout21);

		$this->container->shouldReceive('findByTag')
			->once()
			->with(LayoutManager::TAG_LAYOUT)
			->andReturn(['voonne.layout21Factory' => true]);


		$this->container->shouldReceive('getService')
			->once()
			->with('voonne.layout21Factory')
			->andReturn($layout21Factory);

		$this->expectException(NotRegisteredException::class);
		$this->layoutManager->getLayout(Layout1::class);
	}

}
