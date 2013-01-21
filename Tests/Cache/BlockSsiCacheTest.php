<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Cache;

use Symfony\Cmf\Bundle\BlockBundle\Cache\BlockSsiCache;

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

        $cache = new BlockSsiCache(array(), $router, $blockRenderer, $blockLoader);

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

        $cache = new BlockSsiCache(array(), $router, $blockRenderer, $blockLoader);

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

        $this->assertEquals('<!--# include virtual="/symfony-cmf/block/cache/ssi/XXXXX/%2Fcms%2Fcontent%2Fhome%2FadditionalInfoBlock?updated_at=as" -->', $cacheElement->getData()->getContent());
    }

}