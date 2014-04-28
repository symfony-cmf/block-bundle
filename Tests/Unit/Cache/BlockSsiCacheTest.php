<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Unit\Cache;

use Symfony\Cmf\Bundle\BlockBundle\Cache\BlockSsiCache;

use Symfony\Component\HttpFoundation\Request;

class BlockSsiCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \RuntimeException
     * @dataProvider      getExceptionCacheKeys
     */
    public function testExceptions($keys)
    {
        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');

        $blockRenderer = $this->getMock('Sonata\BlockBundle\Block\BlockRendererInterface');

        $blockLoader = $this->getMock('Sonata\BlockBundle\Block\BlockLoaderInterface');

        $blockContextManager = $this->getMock('Sonata\BlockBundle\Block\BlockContextManagerInterface');

        $cache = new BlockSsiCache('My Token', $router, $blockRenderer, $blockLoader, $blockContextManager);

        $cache->get($keys, 'data');
    }

    public static function getExceptionCacheKeys()
    {
        return array(
            array(array()),
            array(array('block_id' => '/cms/content/home/additionalInfoBlock')),
            array(array('updated_at' => 'foo')),
        );
    }

    public function testInitCache()
    {
        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $router->expects($this->any())->method('generate')->will($this->returnValue('/symfony-cmf/block/cache/ssi/XXXXX/%2Fcms%2Fcontent%2Fhome%2FadditionalInfoBlock?updated_at=as'));

        $blockRenderer = $this->getMock('Sonata\BlockBundle\Block\BlockRendererInterface');

        $blockLoader = $this->getMock('Sonata\BlockBundle\Block\BlockLoaderInterface');

        $blockContextManager = $this->getMock('Sonata\BlockBundle\Block\BlockContextManagerInterface');

        $cache = new BlockSsiCache('My Token', $router, $blockRenderer, $blockLoader, $blockContextManager);

        $this->assertTrue($cache->flush(array()));
        $this->assertTrue($cache->flushAll());

        $keys = array(
            'block_id'   => '/cms/content/home/additionalInfoBlock',
            'updated_at' => 'as',
        );

        $cacheElement = $cache->set($keys, 'data');

        $this->assertInstanceOf('Sonata\Cache\CacheElement', $cacheElement);

        $this->assertTrue($cache->has(array('id' => 7)));

        $cacheElement = $cache->get($keys);

        $this->assertInstanceOf('Sonata\Cache\CacheElement', $cacheElement);

        $this->assertEquals('<!--# include virtual="/symfony-cmf/block/cache/ssi/XXXXX/%2Fcms%2Fcontent%2Fhome%2FadditionalInfoBlock?updated_at=as" -->', $cacheElement->getData()->getContent());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function testAccessDenied()
    {
        $token = 'My Token';
        $keys = array(
            'block_id'   => '/cms/content/home/additionalInfoBlock',
            'updated_at' => 'as'
        );

        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');

        $blockRenderer = $this->getMock('Sonata\BlockBundle\Block\BlockRendererInterface');

        $blockLoader = $this->getMock('Sonata\BlockBundle\Block\BlockLoaderInterface');

        $blockContextManager = $this->getMock('Sonata\BlockBundle\Block\BlockContextManagerInterface');

        $cache = new BlockSsiCache($token, $router, $blockRenderer, $blockLoader, $blockContextManager);

        $request = new Request($keys, array(), array('_token' => 'XXXXX'));

        $cache->cacheAction($request);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testBlockNotFound()
    {
        $token = 'My Token';
        $keys = array(
            'block_id'   => '/not/found',
            'updated_at' => 'as'
        );

        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');

        $blockRenderer = $this->getMock('Sonata\BlockBundle\Block\BlockRendererInterface');

        $blockLoader = $this->getMock('Sonata\BlockBundle\Block\BlockLoaderInterface');

        $blockContextManager = $this->getMock('Sonata\BlockBundle\Block\BlockContextManagerInterface');

        $cache = new BlockSsiCache($token, $router, $blockRenderer, $blockLoader, $blockContextManager);

        $refCache = new \ReflectionClass($cache);
        $refComputeHash = $refCache->getMethod('computeHash');
        $refComputeHash->setAccessible(true);
        $computedToken = $refComputeHash->invokeArgs($cache, array($keys));

        $request = new Request($keys, array(), array('_token' => $computedToken));

        $cache->cacheAction($request);
    }
}
