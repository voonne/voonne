<?php

namespace Voonne\TestVoonne\Model\Facades;

use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Mockery;
use Mockery\MockInterface;
use UnitTester;
use Voonne\Voonne\DuplicateEntryException;
use Voonne\Voonne\Model\Entities\Role;
use Voonne\Voonne\Model\Facades\RoleFacade;
use Voonne\Voonne\Model\Repositories\RoleRepository;


class RoleFacadeTest extends Unit
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
	private $roleRepository;

	/**
	 * @var RoleFacade
	 */
	private $roleFacade;


	protected function _before()
	{
		$this->entityManager = Mockery::mock(EntityManagerInterface::class);
		$this->roleRepository = Mockery::mock(RoleRepository::class);

		$this->roleFacade = new RoleFacade($this->entityManager, $this->roleRepository);
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testSave()
	{
		$role = Mockery::mock(Role::class);
		$unitOfWork = Mockery::mock(UnitOfWork::class);

		$role->shouldReceive('getName')
			->once()
			->withNoArgs()
			->andReturn('Admin');

		$unitOfWork->shouldReceive('getEntityState')
			->once()
			->with($role)
			->andReturn(UnitOfWork::STATE_NEW);

		$this->roleRepository->shouldReceive('countBy')
			->once()
			->with(['name' => 'Admin'])
			->andReturn(0);

		$this->entityManager->shouldReceive('persist')
			->once()
			->with($role);

		$this->entityManager->shouldReceive('flush')
			->once()
			->withNoArgs();

		$this->entityManager->shouldReceive('getUnitOfWork')
			->once()
			->withNoArgs()
			->andReturn($unitOfWork);

		$this->roleFacade->save($role);
	}


	public function testSaveDuplicateEntry()
	{
		$role = Mockery::mock(Role::class);
		$unitOfWork = Mockery::mock(UnitOfWork::class);

		$role->shouldReceive('getName')
			->once()
			->withNoArgs()
			->andReturn('Admin');

		$unitOfWork->shouldReceive('getEntityState')
			->once()
			->with($role)
			->andReturn(UnitOfWork::STATE_NEW);

		$this->roleRepository->shouldReceive('countBy')
			->once()
			->with(['name' => 'Admin'])
			->andReturn(1);

		$this->entityManager->shouldReceive('getUnitOfWork')
			->once()
			->withNoArgs()
			->andReturn($unitOfWork);

		$this->expectException(DuplicateEntryException::class);
		$this->roleFacade->save($role);
	}

}
