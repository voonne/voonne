<?php

namespace Voonne\TestVoonne\Model\Entities;

use Codeception\Test\Unit;
use Kdyby\Doctrine\Collections\ReadOnlyCollectionWrapper;
use Mockery;
use UnitTester;
use Voonne\Voonne\Model\Entities\Privilege;
use Voonne\Voonne\Model\Entities\Role;


class RoleTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var Role
	 */
	private $role;


	protected function _before()
	{
		$this->role = new Role('admin');
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testInitialize()
	{
		$this->assertEquals('admin', $this->role->getName());
		$this->assertInstanceOf(ReadOnlyCollectionWrapper::class, $this->role->getPrivileges());
		$this->assertInstanceOf(ReadOnlyCollectionWrapper::class, $this->role->getUsers());
	}


	public function testAddRemovePrivilege()
	{
		$privilege1 = Mockery::mock(Privilege::class);
		$privilege2 = Mockery::mock(Privilege::class);

		$this->assertEquals([], $this->role->getPrivileges()->toArray());

		$this->role->addPrivilege($privilege1);

		$this->assertEquals([$privilege1], $this->role->getPrivileges()->toArray());

		$this->role->addPrivilege($privilege2);

		$this->assertEquals([$privilege1, $privilege2], $this->role->getPrivileges()->toArray());

		$this->role->removePrivilege($privilege2);

		$this->assertEquals([$privilege1], $this->role->getPrivileges()->toArray());
	}


	public function testUpdate()
	{
		$this->role->update('admin1');

		$this->assertEquals('admin1', $this->role->getName());
		$this->assertInstanceOf(ReadOnlyCollectionWrapper::class, $this->role->getPrivileges());
		$this->assertInstanceOf(ReadOnlyCollectionWrapper::class, $this->role->getUsers());
	}

}
