<?php

namespace Voonne\TestVoonne\Model\Entities;

use Codeception\Test\Unit;
use DateTime;
use Kdyby\Doctrine\Collections\ReadOnlyCollectionWrapper;
use Mockery;
use UnitTester;
use Voonne\Voonne\Model\Entities\Role;
use Voonne\Voonne\Model\Entities\User;


class UserTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var User
	 */
	private $user;


	protected function _before()
	{
		$this->user = new User('john@example.com', 'password');
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testInitialize()
	{
		$this->assertEquals('john@example.com', $this->user->getEmail());
		$this->assertNotNull($this->user->getPassword());
		$this->assertInstanceOf(DateTime::class, $this->user->getCreatedAt());
		$this->assertInstanceOf(ReadOnlyCollectionWrapper::class, $this->user->getRoles());
	}


	public function testUpdate()
	{
		$this->user->update('george@example.com');

		$this->assertEquals('george@example.com', $this->user->getEmail());
		$this->assertNotNull($this->user->getPassword());
		$this->assertInstanceOf(DateTime::class, $this->user->getCreatedAt());
	}


	public function testAddRemoveRole()
	{
		$role1 = Mockery::mock(Role::class);
		$role2 = Mockery::mock(Role::class);

		$this->assertEquals([], $this->user->getRoles()->toArray());

		$this->user->addRole($role1);

		$this->assertEquals([$role1], $this->user->getRoles()->toArray());

		$this->user->addRole($role2);

		$this->assertEquals([$role1, $role2], $this->user->getRoles()->toArray());

		$this->user->removeRole($role2);

		$this->assertEquals([$role1], $this->user->getRoles()->toArray());
	}

}
