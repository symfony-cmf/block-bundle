<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2013 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Functional\Block;

use Sonata\BlockBundle\Block\BlockContext;
use Symfony\Cmf\Bundle\BlockBundle\Block\MenuBlockService,
    Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\MenuBlock,
    Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SimpleBlock;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode;

class MenuBlockServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testExecutionOfDisabledBlock()
    {
        $menuBlock = new MenuBlock();
        $menuBlock->setEnabled(false);

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

        $menuBlockService = new MenuBlockService('test-service', $templatingMock, $blockRendererMock, $blockContextManagerMock);
        $menuBlockService->execute(new BlockContext($menuBlock));
    }

    public function testExecutionOfEnabledBlock()
    {
        $menuNode = new MenuNode();

        $menuBlock = new MenuBlock();
        $menuBlock->setEnabled(true);
        $menuBlock->setReferencedMenu($menuNode);

        $menuBlockContext = new BlockContext($menuBlock);

        $templatingMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $blockRendererMock = $this->getMockBuilder('Sonata\BlockBundle\Block\BlockRendererInterface')
            ->disableOriginalConstructor()
            ->getMock();
        
        
        $blockContextManagerMock = $this->getMockBuilder('Sonata\BlockBundle\Block\BlockContextManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        
        $menuBlockService = new MenuBlockService('test-service', $templatingMock, $blockRendererMock, $blockContextManagerMock);
        $menuBlockService->execute($menuBlockContext);
    }

}
