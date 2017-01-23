<?php

namespace Voonne\TestVoonne\Model\Entities;

use Codeception\Test\Unit;
use Kdyby\Doctrine\Collections\ReadOnlyCollectionWrapper;
use Mockery;
use UnitTester;
use Voonne\Voonne\Model\Entities\Privilege;
use Voonne\Voonne\Model\Entities\Resource;


class PrivilegeTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var Resource
	 */
	private $resource;

	/**
	 * @var Privilege
	 */
	private $privilege;


	protected function _before()
	{
		$this->resource = Mockery::mock(Resource::class);

		$this->privilege = new Privilege('view', 'Can view list of users', $this->resource);
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testInitialize()
	{
		$this->assertEquals('view', $this->privilege->getName());
		$this->assertEquals('Can view list of users', $this->privilege->getDescription());
		$this->assertEquals($this->resource, $this->privilege->getResource());
		$this->assertInstanceOf(ReadOnlyCollectionWrapper::class, $this->privilege->getRoles());
	}

}
