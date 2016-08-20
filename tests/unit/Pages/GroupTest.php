<?php

namespace Voonne\Voonne\Pages;

use Codeception\Test\Unit;
use Mockery;
use UnitTester;


class GroupTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var Group
	 */
	private $group;


	protected function _before()
	{
		$this->group = new Group('name', 'label');
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testInitialize()
	{
		$this->assertEquals('name', $this->group->getName());
		$this->assertEquals('label', $this->group->getTitle());
		$this->assertNull($this->group->getIcon());
	}


	public function testAddPage()
	{
		$page1 = Mockery::mock(Page::class);
		$page2 = Mockery::mock(Page::class);

		$page1->shouldReceive('getName')
			->twice()
			->withNoArgs()
			->andReturn('page1');

		$page1->shouldReceive('setParent')
			->once()
			->with($this->group);

		$page2->shouldReceive('getName')
			->twice()
			->withNoArgs()
			->andReturn('page2');

		$page2->shouldReceive('setParent')
			->once()
			->with($this->group);

		$this->group->addPage($page1);
		$this->group->addPage($page2);

		$this->assertEquals([
			'page1' => $page1,
			'page2' => $page2
		], $this->group->getPages());
	}


	public function testGetStructure()
	{
		$page1 = Mockery::mock(Page::class);
		$page2 = Mockery::mock(Page::class);
		$page3 = Mockery::mock(Page::class);
		$page4 = Mockery::mock(Page::class);

		$page1->shouldReceive('getName')
			->twice()
			->withNoArgs()
			->andReturn('page1');

		$page1->shouldReceive('setParent')
			->once()
			->with($this->group);

		$page1->shouldReceive('getPath')
			->once()
			->withNoArgs()
			->andReturn('page1');

		$page1->shouldReceive('getPages')
			->once()
			->withNoArgs()
			->andReturn([]);

		$page2->shouldReceive('getName')
			->twice()
			->withNoArgs()
			->andReturn('page2');

		$page2->shouldReceive('setParent')
			->once()
			->with($this->group);

		$page2->shouldReceive('getPath')
			->once()
			->withNoArgs()
			->andReturn('page2');

		$page2->shouldReceive('getPages')
			->twice()
			->withNoArgs()
			->andReturn(['page3' => $page3, 'page4' => $page4]);

		$page3->shouldReceive('getPath')
			->once()
			->withNoArgs()
			->andReturn('page2.page3');

		$page3->shouldReceive('getPages')
			->once()
			->withNoArgs()
			->andReturn([]);

		$page4->shouldReceive('getPath')
			->once()
			->withNoArgs()
			->andReturn('page2.page4');

		$page4->shouldReceive('getPages')
			->once()
			->withNoArgs()
			->andReturn([]);

		$this->group->addPage($page1);
		$this->group->addPage($page2);

		$this->assertEquals([
			'page1' => $page1,
			'page2' => $page2,
			'page2.page3' => $page3,
			'page2.page4' => $page4
		], $this->group->getStructure());
	}

}
