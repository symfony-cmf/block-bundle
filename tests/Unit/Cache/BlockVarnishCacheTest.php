<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Unit\Cache;

use Sonata\BlockBundle\Block\BlockContextManagerInterface;
use Sonata\BlockBundle\Block\BlockLoaderInterface;
use Sonata\BlockBundle\Block\BlockRendererInterface;
use Symfony\Cmf\Bundle\BlockBundle\Cache\BlockVarnishCache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Symfony\Component\Routing\RouterInterface;

class BlockVarnishCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FragmentHandler|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fragmentHandler;

    public function setUp()
    {
        $this->fragmentHandler = $this->createMock(FragmentHandler::class);
    }

    /**
     * @expectedException \RuntimeException
     * @dataProvider      getExceptionCacheKeys
     */
    public function testExceptions($keys)
    {
        $router = $this->createMock(RouterInterface::class);

        $blockRenderer = $this->createMock(BlockRendererInterface::class);

        $blockLoader = $this->createMock(BlockLoaderInterface::class);

        $blockContextManager = $this->createMock(BlockContextManagerInterface::class);

        $cache = new BlockVarnishCache('My Token', $router, $blockRenderer, $blockLoader, $blockContextManager, $this->fragmentHandler, [], 'ban');

        $cache->get($keys, 'data');
    }

    public static function getExceptionCacheKeys()
    {
        return [
            [[]],
            [['block_id' => '/cms/content/home/additionalInfoBlock']],
            [['updated_at' => 'foo']],
        ];
    }

    public function testInitCache()
    {
        $router = $this->createMock(RouterInterface::class);
        $router->expects($this->any())->method('generate')->will($this->returnValue('http://cmf.symfony.com/symfony-cmf/block/cache/varnish/XXX/%2Fcms%2Fcontent%2Fhome%2FadditionalInfoBlock?updated_at=as'));

        $blockRenderer = $this->createMock(BlockRendererInterface::class);

        $blockLoader = $this->createMock(BlockLoaderInterface::class);

        $blockContextManager = $this->createMock(BlockContextManagerInterface::class);

        $content = '<esi:include src="http://cmf.symfony.com/symfony-cmf/block/cache/varnish/XXX/%2Fcms%2Fcontent%2Fhome%2FadditionalInfoBlock?updated_at=as';

        $this->fragmentHandler
            ->expects($this->once())
            ->method('render')
            ->will($this->returnValue($content))
        ;

        $cache = new BlockVarnishCache('My Token', $router, $blockRenderer, $blockLoader, $blockContextManager, $this->fragmentHandler, [], 'ban');

        $this->assertTrue($cache->flush([]));
        $this->assertTrue($cache->flushAll());

        $keys = [
            'block_id' => '/cms/content/home/additionalInfoBlock',
            'updated_at' => 'as',
        ];

        $cacheElement = $cache->set($keys, 'data');

        $this->assertInstanceOf('Sonata\Cache\CacheElement', $cacheElement);

        $this->assertTrue($cache->has(['id' => 7]));

        $cacheElement = $cache->get($keys);

        $this->assertInstanceOf('Sonata\Cache\CacheElement', $cacheElement);

        $this->assertEquals($content, $cacheElement->getData()->getContent());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function testAccessDenied()
    {
        $token = 'My Token';
        $keys = [
            'block_id' => '/cms/content/home/additionalInfoBlock',
            'updated_at' => 'as',
        ];

        $router = $this->createMock(RouterInterface::class);

        $blockRenderer = $this->createMock(BlockRendererInterface::class);

        $blockLoader = $this->createMock(BlockLoaderInterface::class);

        $blockContextManager = $this->createMock(BlockContextManagerInterface::class);

        $cache = new BlockVarnishCache($token, $router, $blockRenderer, $blockLoader, $blockContextManager, $this->fragmentHandler, [], 'ban');

        $request = new Request($keys, [], ['_token' => 'XXX']);

        $cache->cacheAction($request);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testBlockNotFound()
    {
        $token = 'My Token';
        $keys = [
            'block_id' => '/not/found',
            'updated_at' => 'as',
        ];

        $router = $this->createMock(RouterInterface::class);

        $blockRenderer = $this->createMock(BlockRendererInterface::class);

        $blockLoader = $this->createMock(BlockLoaderInterface::class);

        $blockContextManager = $this->createMock(BlockContextManagerInterface::class);

        $cache = new BlockVarnishCache($token, $router, $blockRenderer, $blockLoader, $blockContextManager, $this->fragmentHandler, [], 'ban');

        $refCache = new \ReflectionClass($cache);
        $refComputeHash = $refCache->getMethod('computeHash');
        $refComputeHash->setAccessible(true);
        $computedToken = $refComputeHash->invokeArgs($cache, [$keys]);

        $request = new Request($keys, [], ['_token' => $computedToken]);

        $cache->cacheAction($request);
    }
}
