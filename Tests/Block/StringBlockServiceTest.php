<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Block;

use Symfony\Cmf\Bundle\BlockBundle\Block\StringBlockService,
    Symfony\Cmf\Bundle\BlockBundle\Document\StringBlock;

class StringBlockServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testExecutionOfEnabledBlock()
    {
        $stringBlock = new StringBlock();
        $stringBlock->setEnabled(true);

        $templatingMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $templatingMock->expects($this->once())
            ->method('renderResponse')
            ->with(
                $this->equalTo('SymfonyCmfBlockBundle:Block:block_string.html.twig'),
                $this->equalTo(array(
                    'block'=> $stringBlock
                ))
            );

        $stringBlockService = new StringBlockService('test-service', $templatingMock);
        $stringBlockService->execute($stringBlock);
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
        $stringBlockService->execute($stringBlock);
    }

}
