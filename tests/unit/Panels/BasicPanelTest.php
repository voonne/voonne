<?php

namespace Voonne\Voonne\Panels;

use Codeception\Test\Unit;
use Mockery;
use Mockery\MockInterface;
use Nette\Localization\ITranslator;
use UnitTester;


class BasicPanelTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var MockInterface
	 */
	private $translator;

	/**
	 * @var BasicPanel
	 */
	private $basicPanel;


	protected function _before()
	{
		$this->translator = Mockery::mock(ITranslator::class);

		$this->basicPanel = new TestBasicPanel($this->translator);
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testSetTitle()
	{
		$this->assertNull($this->basicPanel->getTitle());

		$this->basicPanel->setTitle('title');

		$this->assertEquals('title', $this->basicPanel->getTitle());
	}

}


class TestBasicPanel extends BasicPanel
{

}
