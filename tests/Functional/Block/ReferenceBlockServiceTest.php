<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Functional\Block;

use Sonata\BlockBundle\Block\BlockContext;
use Sonata\BlockBundle\Block\BlockContextManagerInterface;
use Sonata\BlockBundle\Block\BlockRendererInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Cmf\Bundle\BlockBundle\Block\ReferenceBlockService;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ReferenceBlock;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SimpleBlock;

class ReferenceBlockServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testExecutionOfDisabledBlock()
    {
        $referenceBlock = new ReferenceBlock();
        $referenceBlock->setEnabled(false);

        $templatingMock = $this->createMock(EngineInterface::class);

        $blockRendererMock = $this->createMock(BlockRendererInterface::class);
        $blockRendererMock->expects($this->never())
             ->method('render');
        $blockContextManagerMock = $this->createMock(BlockContextManagerInterface::class);

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

        $templatingMock = $this->createMock(EngineInterface::class);

        $blockRendererMock = $this->createMock(BlockRendererInterface::class);
        $blockRendererMock->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo($simpleBlockContext)
            );
        $blockContextManagerMock = $this->createMock(BlockContextManagerInterface::class);
        $blockContextManagerMock->expects($this->once())
            ->method('get')
            ->will(
                $this->returnValue($simpleBlockContext)
            );

        $referenceBlockService = new ReferenceBlockService('test-service', $templatingMock, $blockRendererMock, $blockContextManagerMock);
        $referenceBlockService->execute($referenceBlockContext);
    }
}
