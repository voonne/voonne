<?php

namespace Voonne\TestVoonne\Model\Entities;

use Codeception\Test\Unit;
use Kdyby\Doctrine\Collections\ReadOnlyCollectionWrapper;
use Mockery;
use UnitTester;
use Voonne\Voonne\Model\Entities\Zone;


class ZoneTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var Zone
	 */
	private $zone;


	protected function _before()
	{
		$this->zone = new Zone('admin', 'Administration');
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testInitialize()
	{
		$this->assertEquals('admin', $this->zone->getName());
		$this->assertEquals('Administration', $this->zone->getDescription());
		$this->assertInstanceOf(ReadOnlyCollectionWrapper::class, $this->zone->getResources());
	}

}
