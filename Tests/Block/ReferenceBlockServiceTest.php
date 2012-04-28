<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Block;

use Symfony\Cmf\Bundle\BlockBundle\Block\ReferenceBlockService,
    Symfony\Cmf\Bundle\BlockBundle\Document\ReferenceBlock,
    Symfony\Cmf\Bundle\BlockBundle\Document\SimpleBlock;

class ReferenceBlockServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testExecutionOfDisabledBlock()
    {
        $referenceBlock = new ReferenceBlock();
        $referenceBlock->setEnabled(false);

        $templatingMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $blockRendererMock = $this->getMockBuilder('Sonata\BlockBundle\Block\BlockRendererInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $blockRendererMock->expects($this->never())
             ->method('render');

        $referenceBlockService = new ReferenceBlockService('test-service', $templatingMock, $blockRendererMock);
        $referenceBlockService->execute($referenceBlock);
    }

    public function testExecutionOfEnabledBlock()
    {
        $simpleBlock = new SimpleBlock();

        $referenceBlock = new ReferenceBlock();
        $referenceBlock->setEnabled(true);
        $referenceBlock->setReferencedBlock($simpleBlock);

        $templatingMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $blockRendererMock = $this->getMockBuilder('Sonata\BlockBundle\Block\BlockRendererInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $blockRendererMock->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo($simpleBlock)
            );

        $referenceBlockService = new ReferenceBlockService('test-service', $templatingMock, $blockRendererMock);
        $referenceBlockService->execute($referenceBlock);
    }

}
