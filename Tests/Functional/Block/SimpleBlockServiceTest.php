<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Functional\Block;

use Sonata\BlockBundle\Block\BlockContext;
use Symfony\Cmf\Bundle\BlockBundle\Block\SimpleBlockService;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SimpleBlock;

class SimpleBlockServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testExecutionOfEnabledBlock()
    {
        $template = 'CmfBlockBundle:Block:block_simple.html.twig';
        $simpleBlock = new SimpleBlock();
        $simpleBlock->setEnabled(true);
        $blockContext = new BlockContext($simpleBlock, array('template' => $template));

        $templatingMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $templatingMock->expects($this->once())
            ->method('renderResponse')
            ->with(
                $this->equalTo($template),
                $this->equalTo(array(
                    'block' => $simpleBlock,
                ))
            );

        $simpleBlockService = new SimpleBlockService('test-service', $templatingMock, $template);
        $simpleBlockService->execute($blockContext);
    }

    public function testExecutionOfDisabledBlock()
    {
        $simpleBlock = new SimpleBlock();
        $simpleBlock->setEnabled(false);

        $templatingMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $templatingMock->expects($this->never())
             ->method('renderResponse');

        $simpleBlockService = new SimpleBlockService('test-service', $templatingMock);
        $simpleBlockService->execute(new BlockContext($simpleBlock));
    }
}
