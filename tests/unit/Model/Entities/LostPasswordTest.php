<?php

namespace Voonne\TestVoonne\Model\Entities;

use Codeception\Test\Unit;
use DateTime;
use Mockery;
use Mockery\MockInterface;
use UnitTester;
use Voonne\Voonne\Model\Entities\LostPassword;
use Voonne\Voonne\Model\Entities\User;


class LostPasswordTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var MockInterface
	 */
	private $user;

	/**
	 * @var LostPassword
	 */
	private $lostPassword;


	protected function _before()
	{
		$this->user = Mockery::mock(User::class);

		$this->lostPassword = new LostPassword($this->user);
	}


	protected function _after()
	{
		Mockery::mock();
	}


	public function testInitialize()
	{
		$this->assertEquals($this->user, $this->lostPassword->getUser());
		$this->assertNotEmpty($this->lostPassword->getCode());
		$this->assertInstanceOf(DateTime::class, $this->lostPassword->getCreatedAt());
	}

}
