<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Unit\Templating\Helper;

use Symfony\Cmf\Bundle\BlockBundle\Templating\Helper\CmfBlockHelper;

class CmfBlockHelperTest extends \PHPUnit_Framework_TestCase
{
    private $sonataBlock;

    /**
     * @dataProvider getEmbedBlockData
     *
     * @param string $input     The input which the helper should evaluate
     * @param string $blockname The name of the block which the helper should find
     */
    public function testEmbedBlock($input, $blockname)
    {
        $this->getSonataBlock()->expects($this->once())
            ->method('render')
            ->with($this->equalTo(array('name' => $blockname)));

        $helper = new CmfBlockHelper($this->getSonataBlock(), '%embed-block:"', '"%');

        $helper->embedBlocks($input);
    }

    public function getEmbedBlockData()
    {
        return array(
            array('<span>%embed-block:"/absolute/path/to/block"%</span>', '/absolute/path/to/block'),
            array('%embed-block:"local-block"%', 'local-block'),
            array('Lorem ipsum dolor mir %embed-block:"foo"% sublim da kalir.', 'foo'),
            array('%embed-block:foo% bar %embed-block:"cat"%', 'cat'),
        );
    }

    public function testLogsIfSonataThrowsException()
    {
        $logger = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $logger->expects($this->once())
            ->method('warn')
            ->with($this->matchesRegularExpression('/^Failed to render block "foo" embedded in content: /'));

        $exception = $this->getMock('Sonata\BlockBundle\Exception\BlockNotFoundException', array('getMessage'));

        $this->getSonataBlock()->expects($this->once())
            ->method('render')
            ->will($this->throwException($exception));

        $helper = new CmfBlockHelper($this->getSonataBlock(), '%embed-block:"', '"%', $logger);
        $helper->embedBlocks('%embed-block:"foo"%');
    }

    /**
     * @dataProvider blockDelimitersData
     *
     * @param $prefix
     * @param $postfix
     */
    public function testMultipleEmbedBlocks($prefix, $postfix)
    {
        $this->getSonataBlock()
            ->method('render')
            ->with($this->equalTo(array('name' => 'foo')));

        $this->getSonataBlock()
            ->method('render')
            ->with($this->equalTo(array('name' => 'cat')));

        $helper = new CmfBlockHelper($this->getSonataBlock(), $prefix, $postfix);
        $helper->embedBlocks('%embed-block:"foo"% bar %embed-block:"cat"%');
    }

    /**
     * @dataProvider segmentizeProvider
     *
     * @param $text
     * @param $expected
     */
    public function testSegmentize($text, $expected)
    {
        $helper = new CmfBlockHelper($this->getSonataBlock(), '%embed-block|', '|end%');
        $actual = $this->callMethod($helper, 'segmentize', array($text));
        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider parseProvider
     *
     * @param $text
     * @param $expected
     */
    public function testParse($text, $expected)
    {
        $helper = $this->getMockBuilder('Symfony\Cmf\Bundle\BlockBundle\Templating\Helper\CmfBlockHelper')
            ->setConstructorArgs(array($this->getSonataBlock(), '%embed-block|', '|end%'))
            ->setMethods(array('render'))
            ->getMock();

        $helper->method('render')
            ->willReturn('this_is_block_content');

        $actual = $this->callMethod($helper, 'parse', array($text));

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function parseProvider()
    {
        return array(
            array(
                '%embed-block|/cms/blocks/test|end%',
                'this_is_block_content',
            ),
            array(
                '<div>%embed-block|/cms/blocks/test|end%<div>%embed-block|/cms/blocks/test|end%</div>',
                '<div>this_is_block_content<div>this_is_block_content</div>',
            ),
            array(
                '/cms/blocks/test|end%<div>/cms/blocks/test|end%</div>',
                '/cms/blocks/test|end%<div>/cms/blocks/test|end%</div>',
            ),
            array(
                "%embed-block|/cms/blocks/test\n\n   |end%<div>/cms/blocks/test|end%</div>",
                'this_is_block_content<div>/cms/blocks/test</div>',
            ),
        );
    }

    /**
     * @return array
     */
    public function segmentizeProvider()
    {
        return array(
            array(
                '%embed-block|/cms/blocks/test|end%',
                array('', array('/cms/blocks/test')),
            ),
            array(
                '<div>%embed-block|/cms/blocks/test|end%</div>',
                array('<div>', array('/cms/blocks/test', '</div>')),
            ),
            array(
                '<div>%embed-block|/cms/blocks/test|end%<div>/cms/blocks/test|end%</div>',
                array('<div>', array('/cms/blocks/test', '<div>/cms/blocks/test', '</div>')),
            ),
            array(
                '/cms/blocks/test|end%<div>/cms/blocks/test|end%</div>',
                array('/cms/blocks/test|end%<div>/cms/blocks/test|end%</div>'),
            ),
            array(
                "%embed-block|/cms/blocks/test\n\n   |end%<div>/cms/blocks/test|end%</div>",
                array('', array('/cms/blocks/test', '<div>/cms/blocks/test', '</div>')),
            ),
            array(
                '<div>%embed-block|/cms/blocks/test1|end%</div><div>%embed-block|/cms/blocks/test2|end%</div><div>%embed-block|/cms/blocks/test3|end%</div>',
                array(
                    '<div>',
                    array('/cms/blocks/test1', '</div><div>'),
                    array('/cms/blocks/test2', '</div><div>'),
                    array('/cms/blocks/test3', '</div>'),
                ),
            ),
            array(
                '<div>%embed-block|/cms/blocks/test1|end%</div><div>/cms/blocks/test2|end%</div><div>%embed-block|/cms/blocks/test3|end%</div>',
                array(
                    '<div>',
                    array('/cms/blocks/test1', '</div><div>/cms/blocks/test2', '</div><div>'),
                    array('/cms/blocks/test3', '</div>'),
                ),
            ),
        );
    }

    /**
     * @return array
     */
    public function blockDelimitersData()
    {
        return array(
            array('%embed-block:"', '"%"'),
            array('%embed-block|', '|end%'),
        );
    }

    protected function getSonataBlock()
    {
        if (null === $this->sonataBlock) {
            $this->setSonataBlock();
        }

        return $this->sonataBlock;
    }

    private function setSonataBlock()
    {
        $this->sonataBlock = $this->getMockBuilder('Sonata\BlockBundle\Templating\Helper\BlockHelper')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param $object
     * @param string $method
     * @param array  $params
     *
     * @return mixed
     */
    private function callMethod($object, $method, array $params)
    {
        $method = new \ReflectionMethod(get_class($object), $method);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $params);
    }
}
