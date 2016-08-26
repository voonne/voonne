<?php

namespace Voonne\Voonne\Model\Entities;

use Codeception\Test\Unit;
use DateTime;
use Mockery;
use UnitTester;


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
	}


	public function testUpdaste()
	{
		$this->user->update('george@example.com');

		$this->assertEquals('george@example.com', $this->user->getEmail());
		$this->assertNotNull($this->user->getPassword());
		$this->assertInstanceOf(DateTime::class, $this->user->getCreatedAt());
	}

}
