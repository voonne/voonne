<?php

namespace Voonne\TestVoonne\Model\Facades;

use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\MockInterface;
use UnitTester;
use Voonne\Voonne\DuplicateEntryException;
use Voonne\Voonne\Model\Entities\User;
use Voonne\Voonne\Model\Facades\UserFacade;
use Voonne\Voonne\Model\Repositories\UserRepository;


class UserFacadeTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var MockInterface
	 */
	private $entityManager;

	/**
	 * @var MockInterface
	 */
	private $userRepository;

	/**
	 * @var UserFacade
	 */
	private $userFacade;


	protected function _before()
	{
		$this->entityManager = Mockery::mock(EntityManagerInterface::class);
		$this->userRepository = Mockery::mock(UserRepository::class);

		$this->userFacade = new UserFacade($this->entityManager, $this->userRepository);
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testSave()
	{
		$user = Mockery::mock(User::class);

		$user->shouldReceive('getEmail')
			->once()
			->withNoArgs()
			->andReturn('new@example.com');

		$this->userRepository->shouldReceive('isEmailFree')
			->once()
			->with($user, 'new@example.com')
			->andReturn(true);

		$this->entityManager->shouldReceive('persist')
			->once()
			->with($user);

		$this->entityManager->shouldReceive('flush')
			->once()
			->withNoArgs();

		$this->userFacade->save($user);
	}


	public function testSaveDuplicateEntry()
	{
		$user = Mockery::mock(User::class);

		$user->shouldReceive('getEmail')
			->once()
			->withNoArgs()
			->andReturn('new@example.com');

		$this->userRepository->shouldReceive('isEmailFree')
			->once()
			->with($user, 'new@example.com')
			->andReturn(false);

		$this->expectException(DuplicateEntryException::class);
		$this->userFacade->save($user);
	}


	public function testRemove()
	{
		$user = Mockery::mock(User::class);

		$this->entityManager->shouldReceive('remove')
			->once()
			->with($user);

		$this->entityManager->shouldReceive('flush')
			->once()
			->withNoArgs();

		$this->userFacade->remove($user);
	}

}
