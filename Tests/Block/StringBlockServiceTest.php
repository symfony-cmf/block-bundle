<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Block;

use Symfony\Cmf\Bundle\BlockBundle\Block\StringBlockService,
    Symfony\Cmf\Bundle\BlockBundle\Document\StringBlock;

class StringBlockServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testExecutionOfEnabledBlock()
    {
        $textBlock = new StringBlock();
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

        $textBlockService = new StringBlockService('test-service', $templatingMock);
        $textBlockService->execute($textBlock);
    }

    public function testExecutionOfDisabledBlock()
    {
        $textBlock = new StringBlock();
        $textBlock->setEnabled(false);

        $templatingMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $templatingMock->expects($this->never())
             ->method('renderResponse');

        $textBlockService = new StringBlockService('test-service', $templatingMock);
        $textBlockService->execute($textBlock);
    }

}
