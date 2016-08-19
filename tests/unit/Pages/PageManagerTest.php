<?php

namespace Voonne\Voonne\Pages;

use Codeception\Test\Unit;
use Mockery;
use UnitTester;
use Voonne\Voonne\DuplicateEntryException;


class PageManagerTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var PageManager
	 */
	private $pageManager;


	protected function _before()
	{
		$this->pageManager = new PageManager();
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testAddGroup()
	{
		$this->pageManager->addGroup('users', 'Users');
		$this->pageManager->addGroup('options', 'Options', 110, 'cog');

		$groups = $this->pageManager->getGroups();

		$this->assertCount(2, $groups);

		$this->assertEquals('Users', $groups['users']->getLabel());
		$this->assertNull($groups['users']->getIcon());

		$this->assertEquals('Options', $groups['options']->getLabel());
		$this->assertEquals('cog', $groups['options']->getIcon());
	}


	public function testAddGroupDuplicateEntry()
	{
		$this->pageManager->addGroup('users', 'Users');

		$this->expectException(DuplicateEntryException::class);
		$this->pageManager->addGroup('users', 'Users');
	}

}
