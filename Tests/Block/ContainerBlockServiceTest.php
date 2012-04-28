<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Block;

use Symfony\Component\HttpFoundation\Response,
    Symfony\Cmf\Bundle\BlockBundle\Block\ContainerBlockService,
    Symfony\Cmf\Bundle\BlockBundle\Document\ContainerBlock,
    Symfony\Cmf\Bundle\BlockBundle\Document\SimpleBlock;

class ContainerBlockServiceTest extends \PHPUnit_Framework_TestCase
{

    public function testExecutionOfDisabledBlock()
    {
        $containerBlock = new ContainerBlock();
        $containerBlock->setEnabled(false);

        $blockRendererMock = $this->getMockBuilder('Sonata\BlockBundle\Block\BlockRendererInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $blockRendererMock->expects($this->never())
            ->method('render');

        $templatingMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $containerBlockService = new ContainerBlockService('test-service', $templatingMock, $blockRendererMock);
        $containerBlockService->execute($containerBlock);
    }

    public function testExecutionOfEnabledBlock()
    {
        $simpleBlock1 = new SimpleBlock();
        $simpleBlock1->setId(1);

        $simpleBlock2 = new SimpleBlock();
        $simpleBlock2->setId(2);

        $childrenCollectionMock = $this->getMockBuilder('Doctrine\ODM\PHPCR\ChildrenCollection')
            ->disableOriginalConstructor()
            ->getMock();
        $childrenCollectionMock->expects($this->once())
            ->method('getValues')
            ->will($this->returnValue(array($simpleBlock1, $simpleBlock2)));

        $containerBlockMock = $this->getMockBuilder('Symfony\Cmf\Bundle\BlockBundle\Document\ContainerBlock')
            ->disableOriginalConstructor()
            ->getMock();
        $containerBlockMock->expects($this->once())
            ->method('getEnabled')
            ->will($this->returnValue(true));
        $containerBlockMock->expects($this->once())
            ->method('getChildren')
            ->will($this->returnValue($childrenCollectionMock));

        $responseContent1 = 'Rendered Simple Block 1.';
        $responseContent2 = 'Rendered Simple Block 2.';

        $blockResponseMap = array(
            $simpleBlock1->getId() => new Response($responseContent1),
            $simpleBlock2->getId() => new Response($responseContent2)
        );

        $blockRendererMock = $this->getMockBuilder('Sonata\BlockBundle\Block\BlockRendererInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $blockRendererMock->expects($this->exactly(2))
            ->method('render')
            ->will($this->returnCallback(function($block) use($blockResponseMap) {
                return $blockResponseMap[$block->getId()];// return the response object that's mapped with the passed block
            }));

        $templatingMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $containerBlockService = new ContainerBlockService('test-service', $templatingMock, $blockRendererMock);
        $this->assertEquals(($responseContent1 . $responseContent2), $containerBlockService->execute($containerBlockMock)->getContent());
    }

}
