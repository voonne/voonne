<?php

namespace Voonne\Voonne\Model\Entities;

use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery;
use UnitTester;


class DomainTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var Domain
	 */
	private $domain;


	protected function _before()
	{
		$this->domain = new Domain('example.com');
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testInitialize()
	{
		$this->assertEquals('example.com', $this->domain->getName());
		$this->assertInstanceOf(ArrayCollection::class, $this->domain->getDomainLanguages());
	}

}
