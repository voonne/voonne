<?php

namespace Model\Facades;

use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\MockInterface;
use UnitTester;
use Voonne\Voonne\Model\Entities\User;
use Voonne\Voonne\Model\Facades\UserFacade;


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
	 * @var UserFacade
	 */
	private $userFacade;


	protected function _before()
	{
		$this->entityManager = Mockery::mock(EntityManagerInterface::class);

		$this->userFacade = new UserFacade($this->entityManager);
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testSave()
	{
		$user = Mockery::mock(User::class);

		$this->entityManager->shouldReceive('persist')
			->once()
			->with($user);

		$this->entityManager->shouldReceive('flush')
			->once()
			->withNoArgs();

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
