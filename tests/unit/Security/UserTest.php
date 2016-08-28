<?php

namespace Voonne\Voonne\Security;

use Codeception\Test\Unit;
use Mockery;
use Mockery\MockInterface;
use UnitTester;
use Voonne\Voonne\InvalidStateException;
use Voonne\Voonne\Model\Repositories\UserRepository;


class UserTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var MockInterface
	 */
	private $netteUser;

	/**
	 * @var MockInterface
	 */
	private $userRepository;

	/**
	 * @var User
	 */
	private $securityUser;


	protected function _before()
	{
		$this->netteUser = Mockery::mock(\Nette\Security\User::class);
		$this->userRepository = Mockery::mock(UserRepository::class);

		$this->securityUser = new User($this->netteUser, $this->userRepository);
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testGetUser()
	{
		$user = Mockery::mock(\Voonne\Voonne\Model\Entities\User::class);

		$this->netteUser->shouldReceive('isLoggedIn')
			->once()
			->withNoArgs()
			->andReturn(true);

		$this->netteUser->shouldReceive('getId')
			->once()
			->withNoArgs()
			->andReturn('1');

		$this->userRepository->shouldReceive('find')
			->once()
			->with('1')
			->andReturn($user);

		$this->assertEquals($user, $this->securityUser->getUser());
	}


	public function testGetUserIsNotSignedIn()
	{
		$this->netteUser->shouldReceive('isLoggedIn')
			->once()
			->withNoArgs()
			->andReturn(false);

		$this->expectException(InvalidStateException::class);
		$this->securityUser->getUser();
	}

}
