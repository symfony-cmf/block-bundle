<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Unit\Templating\Helper;

use Psr\Log\LoggerInterface;
use Sonata\BlockBundle\Exception\BlockNotFoundException;
use Sonata\BlockBundle\Templating\Helper\BlockHelper;
use Symfony\Cmf\Bundle\BlockBundle\Templating\Helper\CmfBlockHelper;
use Symfony\Cmf\Bundle\BlockBundle\Templating\Helper\EmbedBlocksParser;

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
            ->with($this->equalTo(['name' => $blockname]));

        $parser = new EmbedBlocksParser('%embed-block:"', '"%');
        $helper = new CmfBlockHelper($this->getSonataBlock(), $parser);

        $helper->embedBlocks($input);
    }

    public function getEmbedBlockData()
    {
        return [
            ['<span>%embed-block:"/absolute/path/to/block"%</span>', '/absolute/path/to/block'],
            ['%embed-block:"local-block"%', 'local-block'],
            ['Lorem ipsum dolor mir %embed-block:"foo"% sublim da kalir.', 'foo'],
            ['%embed-block:foo% bar %embed-block:"cat"%', 'cat'],
        ];
    }

    public function testLogsIfSonataThrowsException()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('warning')
            ->with($this->matchesRegularExpression('/^Failed to render block "foo" embedded in content: /'));

        $exception = $this->createMock(BlockNotFoundException::class, ['getMessage']);

        $this->getSonataBlock()->expects($this->once())
            ->method('render')
            ->will($this->throwException($exception));

        $parser = new EmbedBlocksParser('%embed-block:"', '"%');
        $helper = new CmfBlockHelper($this->getSonataBlock(), $parser, $logger);
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
        $this->getSonataBlock()->expects($this->at(0))
            ->method('render')
            ->with($this->equalTo(['name' => 'foo']));

        $this->getSonataBlock()->expects($this->at(1))
            ->method('render')
            ->with($this->equalTo(['name' => 'cat']));

        $parser = new EmbedBlocksParser($prefix, $postfix);
        $helper = new CmfBlockHelper($this->getSonataBlock(), $parser);
        $helper->embedBlocks("{$prefix}foo{$postfix} bar {$prefix}cat{$postfix}");
    }

    /**
     * @return array
     */
    public function blockDelimitersData()
    {
        return [
            ['%embed-block:"', '"%"'],
            ['%embed-block|', '|end%'],
        ];
    }

    protected function getSonataBlock()
    {
        if (null === $this->sonataBlock) {
            $this->sonataBlock = $this->createMock(BlockHelper::class);
        }

        return $this->sonataBlock;
    }
}
