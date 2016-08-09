<?php

namespace Voonne\Voonne\Assets;

use Codeception\Test\Unit;
use Mockery;
use UnitTester;


class ResourceTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var \Voonne\Voonne\Assets\Resource
	 */
	private $resource;


	protected function _before()
	{
		$this->resource = new Resource('styles/admin.css', 'a{text-decoration:none;}', 'text/css');
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testInitialize()
	{
		$this->assertEquals('styles/admin.css', $this->resource->getName());
		$this->assertEquals('a{text-decoration:none;}', $this->resource->getContent());
		$this->assertEquals('text/css', $this->resource->getMimeType());
	}

}
