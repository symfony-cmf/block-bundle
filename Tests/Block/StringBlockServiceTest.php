<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Block;

use Sonata\BlockBundle\Block\BlockContext;
use Symfony\Cmf\Bundle\BlockBundle\Block\StringBlockService,
    Symfony\Cmf\Bundle\BlockBundle\Document\StringBlock;

class StringBlockServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testExecutionOfEnabledBlock()
    {
        $template = 'SymfonyCmfBlockBundle:Block:block_string.html.twig';
        $stringBlock = new StringBlock();
        $stringBlock->setEnabled(true);
        $blockContext = new BlockContext($stringBlock, array('template' => $template));

        $templatingMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $templatingMock->expects($this->once())
            ->method('renderResponse')
            ->with(
                $this->equalTo($template),
                $this->equalTo(array(
                    'block'=> $stringBlock
                ))
            );

        $stringBlockService = new StringBlockService('test-service', $templatingMock, $template);
        $stringBlockService->execute($blockContext);
    }

    public function testExecutionOfDisabledBlock()
    {
        $stringBlock = new StringBlock();
        $stringBlock->setEnabled(false);

        $templatingMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $templatingMock->expects($this->never())
             ->method('renderResponse');

        $stringBlockService = new StringBlockService('test-service', $templatingMock);
        $stringBlockService->execute(new BlockContext($stringBlock));
    }

}
