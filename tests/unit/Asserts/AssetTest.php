<?php

namespace Voonne\TestVoonne\Assets;

use Codeception\Test\Unit;
use Mockery;
use UnitTester;
use Voonne\Voonne\Assets\Asset;


class AssetTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var Asset
	 */
	private $asset;


	protected function _before()
	{
		$this->asset = new Asset('styles/admin.css', 'a{text-decoration:none;}', 'text/css');
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testInitialize()
	{
		$this->assertEquals('styles/admin.css', $this->asset->getName());
		$this->assertEquals('a{text-decoration:none;}', $this->asset->getContent());
		$this->assertEquals('text/css', $this->asset->getMimeType());
	}

}
