<?php

namespace Voonne\Voonne\Content;

use Codeception\Test\Unit;
use Mockery;
use Nette\Utils\Strings;
use ReflectionClass;
use stdClass;
use UnitTester;
use Voonne\Voonne\Panels\BlankPanel;


class ContentManagerTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var ContentManager
	 */
	private $contentManager;


	protected function _before()
	{
		$this->contentManager = new ContentManager();
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testAddPanel()
	{
		$panel = Mockery::mock(BlankPanel::class);

		$reflectionClass = new ReflectionClass($panel);

		$this->contentManager->addPanel('users.default', ContentManager::POSITION_CENTER, $panel);

		$this->assertEquals([
			ContentManager::POSITION_TOP => [],
			ContentManager::POSITION_BOTTOM => [],
			ContentManager::POSITION_LEFT => [],
			ContentManager::POSITION_RIGHT => [],
			ContentManager::POSITION_CENTER => [
				Strings::webalize($reflectionClass->getShortName()) => $panel
			]
		], $this->contentManager->getPanels('users.default'));
	}


	public function testGetPanels()
	{
		$panel1 = Mockery::mock(BlankPanel::class);
		$panel2 = Mockery::mock(BlankPanel::class);

		$reflectionClass1 = new ReflectionClass($panel1);
		$reflectionClass2 = new ReflectionClass($panel2);


		$this->contentManager->addPanel('users.default', ContentManager::POSITION_CENTER, $panel1);
		$this->contentManager->addPanel('users.default', ContentManager::POSITION_TOP, $panel2);

		$this->assertEquals([
			ContentManager::POSITION_TOP => [
				Strings::webalize($reflectionClass2->getShortName()) => $panel2
			],
			ContentManager::POSITION_BOTTOM => [],
			ContentManager::POSITION_LEFT => [],
			ContentManager::POSITION_RIGHT => [],
			ContentManager::POSITION_CENTER => [
				Strings::webalize($reflectionClass1->getShortName()) => $panel1
			]
		], $this->contentManager->getPanels('users.default'));

		$this->assertEquals([
			ContentManager::POSITION_TOP => [],
			ContentManager::POSITION_BOTTOM => [],
			ContentManager::POSITION_LEFT => [],
			ContentManager::POSITION_RIGHT => [],
			ContentManager::POSITION_CENTER => []
		], $this->contentManager->getPanels('users.notExists'));
	}

}
