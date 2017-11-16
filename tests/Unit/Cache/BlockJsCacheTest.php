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
use Sonata\Cache\CacheElement;
use Symfony\Cmf\Bundle\BlockBundle\Cache\BlockJsCache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class BlockJsCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \RuntimeException
     * @dataProvider getExceptionCacheKeys
     */
    public function testExceptions($keys)
    {
        $router = $this->createMock(RouterInterface::class);

        $blockRenderer = $this->createMock(BlockRendererInterface::class);

        $blockLoader = $this->createMock(BlockLoaderInterface::class);

        $blockContextManager = $this->createMock(BlockContextManagerInterface::class);

        $cache = new BlockJsCache($router, $blockRenderer, $blockLoader, $blockContextManager, false);

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
        $router->expects($this->once())->method('generate')->will($this->returnValue('http://cmf.symfony.com/symfony-cmf/block/cache/js-async.js'));

        $blockRenderer = $this->createMock(BlockRendererInterface::class);

        $blockLoader = $this->createMock(BlockLoaderInterface::class);

        $blockContextManager = $this->createMock(BlockContextManagerInterface::class);

        $cache = new BlockJsCache($router, $blockRenderer, $blockLoader, $blockContextManager, false);

        $this->assertTrue($cache->flush([]));
        $this->assertTrue($cache->flushAll());

        $keys = [
            'block_id' => '/cms/content/home/additionalInfoBlock',
            'updated_at' => 'as',
        ];

        $cacheElement = $cache->set($keys, 'data');

        $this->assertInstanceOf(CacheElement::class, $cacheElement);

        $this->assertTrue($cache->has(['id' => 7]));

        $cacheElement = $cache->get($keys);

        $this->assertInstanceOf(CacheElement::class, $cacheElement);

        $expected = <<<'EXPECTED'
<div id="block-cms-content-home-additionalInfoBlock" >
    <script type="text/javascript">
        /*<![CDATA[*/

            (function () {
                var b = document.createElement('script');
                b.type = 'text/javascript';
                b.async = true;
                b.src = 'http://cmf.symfony.com/symfony-cmf/block/cache/js-async.js'
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(b, s);
            })();

        /*]]>*/
    </script>
</div>
EXPECTED;

        $this->assertEquals($expected, $cacheElement->getData()->getContent());
    }

    public function testCacheAction()
    {
        $router = $this->createMock(RouterInterface::class);

        $blockRenderer = $this->createMock(BlockRendererInterface::class);

        $blockLoader = $this->createMock(BlockLoaderInterface::class);

        $blockContextManager = $this->createMock(BlockContextManagerInterface::class);

        $cache = new BlockJsCache($router, $blockRenderer, $blockLoader, $blockContextManager, false);

        $request = $this->createMock(Request::class);

        // block not found
        $this->assertEquals(new Response('', 404), $cache->cacheAction($request));
    }
}
