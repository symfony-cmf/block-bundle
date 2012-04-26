<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Block;

use Symfony\Cmf\Bundle\BlockBundle\Block\SimpleBlockService;
use Symfony\Cmf\Bundle\BlockBundle\Document\SimpleBlock;

class SimpleBlockServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testExecutionOfEnabledBlock()
    {
        $simpleBlock = new SimpleBlock();
        $simpleBlock->setEnabled(true);

        $templatingMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $templatingMock->expects($this->once())
            ->method('renderResponse')
            ->with(
                $this->equalTo('SymfonyCmfBlockBundle::block_simple.html.twig'),
                $this->equalTo(array(
                    'block'=> $simpleBlock
                ))
            );

        $simpleBlockService = new SimpleBlockService('test-service', $templatingMock);
        $simpleBlockService->execute($simpleBlock);

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
        $simpleBlockService->execute($simpleBlock);

    }

}
