<?php

namespace Voonne\TestVoonne\Model\Entities;

use Codeception\Test\Unit;
use Mockery;
use UnitTester;
use Voonne\Voonne\Model\Entities\Language;


class LanguageTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var Language
	 */
	private $language;


	protected function _before()
	{
		$this->language = new Language('English', 'en');
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testInitialize()
	{
		$this->assertEquals('English', $this->language->getName());
		$this->assertEquals('en', $this->language->getIsoCode());
	}

}
