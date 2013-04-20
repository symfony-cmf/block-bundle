<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Block;

use Symfony\Cmf\Bundle\BlockBundle\Block\TextBlockService,
    Symfony\Cmf\Bundle\BlockBundle\Document\TextBlock;

class TextBlockServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testExecutionOfEnabledBlock()
    {
        $textBlock = new TextBlock();
        $textBlock->setEnabled(true);

        $templatingMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $templatingMock->expects($this->once())
            ->method('renderResponse')
            ->with(
                $this->equalTo('SymfonyCmfBlockBundle:Block:block_text.html.twig'),
                $this->equalTo(array(
                    'block'=> $textBlock
                ))
            );

        $textBlockService = new TextBlockService('test-service', $templatingMock);
        $textBlockService->execute($textBlock);
    }

    public function testExecutionOfDisabledBlock()
    {
        $textBlock = new TextBlock();
        $textBlock->setEnabled(false);

        $templatingMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $templatingMock->expects($this->never())
             ->method('renderResponse');

        $textBlockService = new TextBlockService('test-service', $templatingMock);
        $textBlockService->execute($textBlock);
    }

}
