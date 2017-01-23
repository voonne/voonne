<?php

namespace Voonne\TestVoonne\Model\Entities;

use Codeception\Test\Unit;
use Kdyby\Doctrine\Collections\ReadOnlyCollectionWrapper;
use Mockery;
use Mockery\MockInterface;
use UnitTester;
use Voonne\Voonne\Model\Entities\Resource;
use Voonne\Voonne\Model\Entities\Zone;


class ResourceTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var MockInterface
	 */
	private $zone;

	/**
	 * @var Resource
	 */
	private $resource;


	protected function _before()
	{
		$this->zone = Mockery::mock(Zone::class);

		$this->resource = new Resource('users', 'Module users', $this->zone);
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testInitialize()
	{
		$this->assertEquals('users', $this->resource->getName());
		$this->assertEquals('Module users', $this->resource->getDescription());
		$this->assertEquals($this->zone, $this->resource->getZone());
		$this->assertInstanceOf(ReadOnlyCollectionWrapper::class, $this->resource->getPrivileges());
	}

}
