<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
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

    public function testMultipleEmbedBlocks()
    {
        $this->getSonataBlock()->expects($this->at(0))
            ->method('render')
            ->with($this->equalTo(array('name' => 'foo')));

        $this->getSonataBlock()->expects($this->at(1))
            ->method('render')
            ->with($this->equalTo(array('name' => 'cat')));

        $helper = new CmfBlockHelper($this->getSonataBlock(), '%embed-block:"', '"%');
        $helper->embedBlocks('%embed-block:"foo"% bar %embed-block:"cat"%');
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
}
