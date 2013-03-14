<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Block;

use Symfony\Component\HttpFoundation\Response,
    Symfony\Cmf\Bundle\BlockBundle\Block\ActionBlockService,
    Symfony\Cmf\Bundle\BlockBundle\Document\ActionBlock;

class ActionBlockServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testExecutionOfDisabledBlock()
    {
        $actionBlock = new ActionBlock();
        $actionBlock->setEnabled(false);
        $actionBlock->setActionName('SymfonyCmfBlockBundle:Test:test');

        $templatingMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $kernelMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\HttpKernel')
            ->disableOriginalConstructor()
            ->getMock();
        $kernelMock->expects($this->never())
            ->method('render');

        $actionBlockService = new ActionBlockService('test-service', $templatingMock, $kernelMock);
        $actionBlockService->execute($actionBlock);
    }

    public function testExecutionOfEnabledBlock()
    {
        $actionBlock = new ActionBlock();
        $actionBlock->setEnabled(true);
        $actionBlock->setActionName('SymfonyCmfBlockBundle:Test:test');

        $actionResponse = new Response("Rendered Action Block.");

        $templatingMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $kernelMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\HttpKernel')
            ->disableOriginalConstructor()
            ->getMock();
        $kernelMock->expects($this->once())
            ->method('render')
            ->will($this->returnValue($actionResponse->getContent()));

        $actionBlockService = new ActionBlockService('test-service', $templatingMock, $kernelMock);
        $this->assertEquals($actionResponse, $actionBlockService->execute($actionBlock));
    }

}
