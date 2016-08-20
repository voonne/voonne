<?php

namespace Voonne\Voonne\Pages;

use Codeception\Test\Unit;
use Mockery;
use stdClass;
use UnitTester;
use Voonne\Voonne\InvalidArgumentException;
use Voonne\Voonne\InvalidStateException;
use Voonne\Voonne\Layouts\Layout1\Layout1;
use Voonne\Voonne\Layouts\Layout21\Layout21;


class PageTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var Page
	 */
	private $page;


	protected function _before()
	{
		$this->page = new Page('name', 'title');
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testInitialize()
	{
		$this->assertEquals('name', $this->page->getName());
		$this->assertEquals('title', $this->page->getTitle());
		$this->assertTrue($this->page->isVisible());
		$this->assertNull($this->page->getParent());
		$this->assertEquals(Layout1::class, $this->page->getLayout());
	}


	public function testVisibility()
	{
		$this->page->hide();

		$this->assertFalse($this->page->isVisible());

		$this->page->show();

		$this->assertTrue($this->page->isVisible());
	}


	public function testLayout()
	{
		$this->page->setLayout(Layout21::class);

		$this->assertEquals(Layout21::class, $this->page->getLayout());

		$this->expectException(InvalidArgumentException::class);
		$this->page->setLayout(stdClass::class);
	}


	public function testSetParent()
	{
		$page = Mockery::mock(Page::class);

		$this->page->setParent($page);

		$this->assertEquals($page, $this->page->getParent());

		$this->expectException(InvalidStateException::class);
		$this->page->setParent($page);
	}


	public function testSetParentBadType()
	{
		$this->expectException(InvalidArgumentException::class);
		$this->page->setParent(new stdClass());
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
			->with($this->page);

		$page2->shouldReceive('getName')
			->twice()
			->withNoArgs()
			->andReturn('page2');

		$page2->shouldReceive('setParent')
			->once()
			->with($this->page);

		$this->page->addPage($page1);
		$this->page->addPage($page2);

		$this->assertEquals([
			'page1' => $page1,
			'page2' => $page2
		], $this->page->getPages());
	}


	public function testGetPath()
	{
		$group = Mockery::mock(Group::class);
		$page = Mockery::mock(Page::class);

		$page->shouldReceive('getParent')
			->once()
			->withNoArgs()
			->andReturn($group);

		$page->shouldReceive('getName')
			->once()
			->withNoArgs()
			->andReturn('default');

		$this->page->setParent($page);

		$this->assertEquals('default.name', $this->page->getPath());
	}

}
