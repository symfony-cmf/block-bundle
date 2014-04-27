<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Functional\Block;

use Sonata\BlockBundle\Block\BlockContext;
use Symfony\Cmf\Bundle\BlockBundle\Block\ReferenceBlockService,
    Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ReferenceBlock,
    Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SimpleBlock;

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
        $blockContextManagerMock = $this->getMockBuilder('Sonata\BlockBundle\Block\BlockContextManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $referenceBlockService = new ReferenceBlockService('test-service', $templatingMock, $blockRendererMock, $blockContextManagerMock);
        $referenceBlockService->execute(new BlockContext($referenceBlock));
    }

    public function testExecutionOfEnabledBlock()
    {
        $simpleBlock = new SimpleBlock();

        $simpleBlockContext = new BlockContext($simpleBlock);

        $referenceBlock = new ReferenceBlock();
        $referenceBlock->setEnabled(true);
        $referenceBlock->setReferencedBlock($simpleBlock);

        $referenceBlockContext = new BlockContext($referenceBlock);

        $templatingMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $blockRendererMock = $this->getMockBuilder('Sonata\BlockBundle\Block\BlockRendererInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $blockRendererMock->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo($simpleBlockContext)
            );
        $blockContextManagerMock = $this->getMockBuilder('Sonata\BlockBundle\Block\BlockContextManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $blockContextManagerMock->expects($this->once())
            ->method('get')
            ->will(
                $this->returnValue($simpleBlockContext)
            );

        $referenceBlockService = new ReferenceBlockService('test-service', $templatingMock, $blockRendererMock, $blockContextManagerMock);
        $referenceBlockService->execute($referenceBlockContext);
    }

}
