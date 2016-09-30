<?php

namespace Voonne\TestVoonne\Model\Facades;

use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Mockery;
use Mockery\MockInterface;
use UnitTester;
use Voonne\Voonne\DuplicateEntryException;
use Voonne\Voonne\Model\Entities\LostPassword;
use Voonne\Voonne\Model\Facades\LostPasswordFacade;
use Voonne\Voonne\Model\Repositories\LostPasswordRepository;


class LostPasswordFacadeTest extends Unit
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
	private $lostPasswordRepository;

	/**
	 * @var LostPasswordFacade
	 */
	private $lostPasswordFacade;


	protected function _before()
	{
		$this->entityManager = Mockery::mock(EntityManagerInterface::class);
		$this->lostPasswordRepository = Mockery::mock(LostPasswordRepository::class);

		$this->lostPasswordFacade = new LostPasswordFacade($this->entityManager, $this->lostPasswordRepository);
	}


	protected function _after()
	{
		Mockery::mock();
	}


	public function testSave()
	{
		$lostPassword = Mockery::mock(LostPassword::class);
		$unitOfWork = Mockery::mock(UnitOfWork::class);

		$lostPassword->shouldReceive('getCode')
			->once()
			->withNoArgs()
			->andReturn('1234567890');

		$unitOfWork->shouldReceive('getEntityState')
			->once()
			->with($lostPassword)
			->andReturn(UnitOfWork::STATE_NEW);

		$this->lostPasswordRepository->shouldReceive('countBy')
			->once()
			->with(['code' => '1234567890'])
			->andReturn(0);

		$this->entityManager->shouldReceive('persist')
			->once()
			->with($lostPassword);

		$this->entityManager->shouldReceive('flush')
			->once()
			->withNoArgs();

		$this->entityManager->shouldReceive('getUnitOfWork')
			->once()
			->withNoArgs()
			->andReturn($unitOfWork);

		$this->lostPasswordFacade->save($lostPassword);
	}


	public function testSaveDuplicateEntry()
	{
		$lostPassword = Mockery::mock(LostPassword::class);
		$unitOfWork = Mockery::mock(UnitOfWork::class);

		$lostPassword->shouldReceive('getCode')
			->once()
			->withNoArgs()
			->andReturn('1234567890');

		$unitOfWork->shouldReceive('getEntityState')
			->once()
			->with($lostPassword)
			->andReturn(UnitOfWork::STATE_NEW);

		$this->lostPasswordRepository->shouldReceive('countBy')
			->once()
			->with(['code' => '1234567890'])
			->andReturn(1);


		$this->entityManager->shouldReceive('getUnitOfWork')
			->once()
			->withNoArgs()
			->andReturn($unitOfWork);

		$this->expectException(DuplicateEntryException::class);
		$this->lostPasswordFacade->save($lostPassword);
	}


	public function testRemove()
	{
		$lostPassword = Mockery::mock(LostPassword::class);

		$this->entityManager->shouldReceive('remove')
			->once()
			->with($lostPassword);

		$this->entityManager->shouldReceive('flush')
			->once()
			->withNoArgs();

		$this->lostPasswordFacade->remove($lostPassword);
	}

}
