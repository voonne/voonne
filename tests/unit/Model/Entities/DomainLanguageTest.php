<?php

namespace Voonne\Voonne\Model\Entities;

use Codeception\Test\Unit;
use Mockery;
use UnitTester;


class DomainLanguageTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var Domain
	 */
	private $domain;

	/**
	 * @var Language
	 */
	private $language;

	/**
	 * @var DomainLanguage
	 */
	private $domainLanguage;


	protected function _before()
	{
		$this->domain = Mockery::mock(Domain::class);
		$this->language = Mockery::mock(Language::class);

		$this->domainLanguage = new DomainLanguage($this->domain, $this->language);
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testInitialize()
	{
		$this->assertEquals($this->domain, $this->domainLanguage->getDomain());
		$this->assertEquals($this->language, $this->domainLanguage->getLanguage());
	}

}
