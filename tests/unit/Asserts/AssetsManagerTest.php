<?php

namespace Voonne\Voonne\Assets;

use Codeception\Test\Unit;
use Mockery;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use UnitTester;
use Voonne\Voonne\FileNotFoundException;


class AssetsManagerTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var vfsStreamDirectory
	 */
	private $root;

	/**
	 * @var AssetsManager
	 */
	private $assetsManager;


	protected function _before()
	{
		$this->root = vfsStream::setup('assets');

		$this->assetsManager = new AssetsManager();
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testAddAsset()
	{
		vfsStream::newFile('text.txt')
			->at($this->root)
			->setContent('text');

		$this->assetsManager->addAsset('texts/text.txt', $this->root->url() . '/text.txt');

		$resource = $this->assetsManager->getResource('texts/text.txt');

		$this->assertEquals('texts/text.txt', $resource->getName());
		$this->assertEquals('text', $resource->getContent());
		$this->assertEquals('text/plain', $resource->getMimeType());

		$this->expectException(FileNotFoundException::class);
		$this->assetsManager->getResource('texts/doNotExist.txt');
	}


	public function testAddAssetFileDoNotExist()
	{
		$this->expectException(FileNotFoundException::class);
		$this->assetsManager->addAsset('texts/doNotExist.txt', $this->root->url() . '/doNotExist.txt');
	}


	public function testAddScript()
	{
		vfsStream::newFile('script1.js')
			->at($this->root)
			->setContent('function test1(){}');

		vfsStream::newFile('script2.js')
			->at($this->root)
			->setContent('function test2(){}');

		$this->assetsManager->addScript('admin', $this->root->url() . '/script1.js');
		$this->assetsManager->addScript('admin', $this->root->url() . '/script2.js');

		$resource = $this->assetsManager->getResource('scripts/admin.js');

		$this->assertEquals('scripts/admin.js', $resource->getName());
		$this->assertEquals('function test1(){}' . PHP_EOL . 'function test2(){}' . PHP_EOL, $resource->getContent());
		$this->assertEquals('application/javascript', $resource->getMimeType());

		$this->expectException(FileNotFoundException::class);
		$this->assetsManager->getResource('scripts/admin1.js');
	}


	public function testAddScriptFileDoNotExist()
	{
		$this->expectException(FileNotFoundException::class);
		$this->assetsManager->addScript('admin', $this->root->url() . '/doNotExist.js');
	}


	public function testAddStyle()
	{
		vfsStream::newFile('style1.css')
			->at($this->root)
			->setContent('.test1{color:red;}');

		vfsStream::newFile('style2.css')
			->at($this->root)
			->setContent('.test2{color:blue;}');

		$this->assetsManager->addStyle('admin', $this->root->url() . '/style1.css');
		$this->assetsManager->addStyle('admin', $this->root->url() . '/style2.css');

		$resource = $this->assetsManager->getResource('styles/admin.css');

		$this->assertEquals('styles/admin.css', $resource->getName());
		$this->assertEquals('.test1{color:red;}' . PHP_EOL . '.test2{color:blue;}' . PHP_EOL, $resource->getContent());
		$this->assertEquals('text/css', $resource->getMimeType());

		$this->expectException(FileNotFoundException::class);
		$this->assetsManager->getResource('styles/admin1.css');
	}


	public function testAddStyleFileDoNotExist()
	{
		$this->expectException(FileNotFoundException::class);
		$this->assetsManager->addScript('admin', $this->root->url() . '/doNotExist.css');
	}

}


function realpath($path)
{
	return $path;
}
