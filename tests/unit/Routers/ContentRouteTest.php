<?php

namespace Voonne\TestVoonne\Routers;

use Codeception\Test\Unit;
use Mockery;
use Mockery\MockInterface;
use Nette\Application\Request;
use Nette\Http\Url;
use Nette\Http\UrlScript;
use UnitTester;
use Voonne\Voonne\Routers\ContentRoute;


class ContentRouteTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var MockInterface
	 */
	private $request;

	/**
	 * @var MockInterface
	 */
	private $appRequest;

	/**
	 * @var MockInterface
	 */
	private $refUrl;

	/**
	 * @var ContentRoute
	 */
	private $contentRoute;


	protected function _before()
	{
		$this->request = Mockery::mock(\Nette\Http\Request::class);
		$this->appRequest = Mockery::mock(Request::class);
		$this->refUrl = Mockery::mock(Url::class);

		$this->contentRoute = new ContentRoute($this->request);
	}


	protected function _after()
	{
		Mockery::close();
	}


	public function testConstructUrlBadPresenter()
	{
		$this->appRequest->shouldReceive('getPresenterName')
			->once()
			->withNoArgs()
			->andReturn('Dashboard');

		$this->assertNull($this->contentRoute->constructUrl($this->appRequest, $this->refUrl));
	}


	public function testConstructUrlMissingParameter()
	{
		$this->appRequest->shouldReceive('getPresenterName')
			->once()
			->withNoArgs()
			->andReturn('Content');

		$this->appRequest->shouldReceive('getParameters')
			->once()
			->withNoArgs()
			->andReturn(['groupName' => 'group1']);

		$this->assertNull($this->contentRoute->constructUrl($this->appRequest, $this->refUrl));
	}


	public function testConstructUrl()
	{
		$urlScript = Mockery::mock(UrlScript::class);

		$this->appRequest->shouldReceive('getPresenterName')
			->once()
			->withNoArgs()
			->andReturn('Content');

		$this->appRequest->shouldReceive('getParameters')
			->once()
			->withNoArgs()
			->andReturn(['groupName' => 'group1', 'pageName' => 'page1']);

		$this->refUrl->shouldReceive('getBasePath')
			->once()
			->withNoArgs()
			->andReturn('');

		$this->request->shouldReceive('getUrl')
			->once()
			->withNoArgs()
			->andReturn($urlScript);

		$urlScript->shouldReceive('getPath')
			->once()
			->withNoArgs()
			->andReturn('admin/group2/page2');

		$this->assertEquals('admin/group1/page1', $this->contentRoute->constructUrl($this->appRequest, $this->refUrl));
	}


	public function testConstructMultiWordUrl()
	{
		$urlScript = Mockery::mock(UrlScript::class);

		$this->appRequest->shouldReceive('getPresenterName')
			->once()
			->withNoArgs()
			->andReturn('Content');

		$this->appRequest->shouldReceive('getParameters')
			->once()
			->withNoArgs()
			->andReturn(['groupName' => 'groupGroup1', 'pageName' => 'pagePage1']);

		$this->refUrl->shouldReceive('getBasePath')
			->once()
			->withNoArgs()
			->andReturn('');

		$this->request->shouldReceive('getUrl')
			->once()
			->withNoArgs()
			->andReturn($urlScript);

		$urlScript->shouldReceive('getPath')
			->once()
			->withNoArgs()
			->andReturn('admin/group2/page2');

		$this->assertEquals('admin/group-group1/page-page1', $this->contentRoute->constructUrl($this->appRequest, $this->refUrl));
	}


	public function testConstructUrlPersistentParameters()
	{
		$urlScript = Mockery::mock(UrlScript::class);

		$this->appRequest->shouldReceive('getPresenterName')
			->once()
			->withNoArgs()
			->andReturn('Content');

		$this->appRequest->shouldReceive('getParameters')
			->once()
			->withNoArgs()
			->andReturn(['groupName' => 'group1', 'pageName' => 'page1']);

		$this->refUrl->shouldReceive('getBasePath')
			->once()
			->withNoArgs()
			->andReturn('');

		$this->request->shouldReceive('getUrl')
			->once()
			->withNoArgs()
			->andReturn($urlScript);

		$urlScript->shouldReceive('getPath')
			->once()
			->withNoArgs()
			->andReturn('admin/group1/page1');

		$this->refUrl->shouldReceive('getQueryParameters')
			->once()
			->withNoArgs()
			->andReturn(['id' => '1']);

		$this->assertEquals('admin/group1/page1?id=1', $this->contentRoute->constructUrl($this->appRequest, $this->refUrl));
	}


	public function testConstructUrlComponentParameters()
	{
		$urlScript = Mockery::mock(UrlScript::class);

		$this->appRequest->shouldReceive('getPresenterName')
			->once()
			->withNoArgs()
			->andReturn('Content');

		$this->appRequest->shouldReceive('getParameters')
			->once()
			->withNoArgs()
			->andReturn(['groupName' => 'group1', 'pageName' => 'page1']);

		$this->refUrl->shouldReceive('getBasePath')
			->once()
			->withNoArgs()
			->andReturn('');

		$this->request->shouldReceive('getUrl')
			->once()
			->withNoArgs()
			->andReturn($urlScript);

		$urlScript->shouldReceive('getPath')
			->once()
			->withNoArgs()
			->andReturn('admin/group1/page1');

		$this->refUrl->shouldReceive('getQueryParameters')
			->once()
			->withNoArgs()
			->andReturn(['id' => '1', 'component-id' => '1', 'component1-id' => '1']);

		$this->assertEquals('admin/group1/page1?id=1', $this->contentRoute->constructUrl($this->appRequest, $this->refUrl));
	}

}
