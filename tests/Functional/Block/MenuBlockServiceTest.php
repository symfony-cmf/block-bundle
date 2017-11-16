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

use Knp\Menu\NodeInterface;
use Sonata\BlockBundle\Block\BlockContext;
use Sonata\BlockBundle\Block\BlockContextManagerInterface;
use Sonata\BlockBundle\Block\BlockRendererInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Cmf\Bundle\BlockBundle\Block\MenuBlockService;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\MenuBlock;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode;

class MenuBlockServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testExecutionOfDisabledBlock()
    {
        $menuBlock = new MenuBlock();
        $menuBlock->setEnabled(false);

        $templatingMock = $this->createMock(EngineInterface::class);

        $blockRendererMock = $this->createMock(BlockRendererInterface::class);
        $blockRendererMock->expects($this->never())->method('render');

        $blockContextManagerMock = $this->createMock(BlockContextManagerInterface::class);

        $menuBlockService = new MenuBlockService('test-service', $templatingMock, $blockRendererMock, $blockContextManagerMock);
        $menuBlockService->execute(new BlockContext($menuBlock));
    }

    public function testExecutionOfEnabledBlock()
    {
        $template = 'CmfBlockBundle:Block:block_menu.html.twig';
        $menuNode = new MenuNode();

        $menuBlock = new MenuBlock();
        $menuBlock->setEnabled(true);
        $menuBlock->setMenuNode($menuNode);

        $menuBlockContext = new BlockContext($menuBlock, ['template' => $template]);

        $templatingMock = $this->createMock(EngineInterface::class);

        $blockRendererMock = $this->createMock(BlockRendererInterface::class);

        $blockContextManagerMock = $this->createMock(BlockContextManagerInterface::class);

        $menuBlockService = new MenuBlockService('test-service', $templatingMock, $blockRendererMock, $blockContextManagerMock);
        $menuBlockService->execute($menuBlockContext);
    }

    public function testSetMenuNode()
    {
        $menuBlock = new MenuBlock();
        $this->assertAttributeEmpty('menuNode', $menuBlock);

        $menuBlock->setMenuNode($this->createMock(NodeInterface::class));
        $this->assertAttributeInstanceOf(NodeInterface::class, 'menuNode', $menuBlock);

        $menuBlock->setMenuNode(null);
        $this->assertAttributeSame(null, 'menuNode', $menuBlock);
    }
}
