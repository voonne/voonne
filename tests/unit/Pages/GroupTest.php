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
		$this->group = new Group('label');
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testInitialize()
	{
		$this->assertEquals('label', $this->group->getLabel());
		$this->assertNull($this->group->getIcon());
	}

}
