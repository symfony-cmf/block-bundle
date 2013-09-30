<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2013 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Unit\Cache;

use Symfony\Cmf\Bundle\BlockBundle\Cache\BlockJsCache;

use Symfony\Component\HttpFoundation\Response;

/**
 *
 */
class BlockJsCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \RuntimeException
     * @dataProvider getExceptionCacheKeys
     */
    public function testExceptions($keys)
    {
        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');

        $blockRenderer = $this->getMock('Sonata\BlockBundle\Block\BlockRendererInterface');

        $blockLoader = $this->getMock('Sonata\BlockBundle\Block\BlockLoaderInterface');

        $blockContextManager = $this->getMock('Sonata\BlockBundle\Block\BlockContextManagerInterface');

        $cache = new BlockJsCache($router, $blockRenderer, $blockLoader, $blockContextManager, false);

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
        $router->expects($this->once())->method('generate')->will($this->returnValue('http://cmf.symfony.com/symfony-cmf/block/cache/js-async.js'));

        $blockRenderer = $this->getMock('Sonata\BlockBundle\Block\BlockRendererInterface');

        $blockLoader = $this->getMock('Sonata\BlockBundle\Block\BlockLoaderInterface');

        $blockContextManager = $this->getMock('Sonata\BlockBundle\Block\BlockContextManagerInterface');

        $cache = new BlockJsCache($router, $blockRenderer, $blockLoader, $blockContextManager, false);

        $this->assertTrue($cache->flush(array()));
        $this->assertTrue($cache->flushAll());

        $keys = array(
            'block_id'   => '/cms/content/home/additionalInfoBlock',
            'updated_at' => 'as',
        );

        $cacheElement = $cache->set($keys, 'data');

        $this->assertInstanceOf('Sonata\CacheBundle\Cache\CacheElement', $cacheElement);

        $this->assertTrue($cache->has(array('id' => 7)));

        $cacheElement = $cache->get($keys);

        $this->assertInstanceOf('Sonata\CacheBundle\Cache\CacheElement', $cacheElement);

        $expected = <<<EXPECTED
<div id="block-cms-content-home-additionalInfoBlock" >
    <script type="text/javascript">
        /*<![CDATA[*/

            (function() {
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
        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');

        $blockRenderer = $this->getMock('Sonata\BlockBundle\Block\BlockRendererInterface');

        $blockLoader = $this->getMock('Sonata\BlockBundle\Block\BlockLoaderInterface');

        $blockContextManager = $this->getMock('Sonata\BlockBundle\Block\BlockContextManagerInterface');

        $cache = new BlockJsCache($router, $blockRenderer, $blockLoader, $blockContextManager, false);

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');

        // block not found
        $this->assertEquals(new Response('', 404), $cache->cacheAction($request));
    }
}
