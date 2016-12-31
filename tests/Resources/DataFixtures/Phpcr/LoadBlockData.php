<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Resources\DataFixtures\Phpcr;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\PHPCR\Document\Generic;
use PHPCR\Util\NodeHelper;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ActionBlock;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ContainerBlock;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\MenuBlock;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ReferenceBlock;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SimpleBlock;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\StringBlock;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\Menu;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode;

/**
 * @author David Buchmann <david@liip.ch>
 */
class LoadBlockData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        NodeHelper::createPath($manager->getPhpcrSession(), '/test');

        $root = $manager->find(null, '/test');
        $parent = new Generic();
        $parent->setParentDocument($root);
        $parent->setNodename('blocks');
        $manager->persist($parent);

        //Simple
        $block = new SimpleBlock();
        $block->setParentDocument($parent);
        $block->setName('block-1');
        $block->setTitle('block-1-title');
        $block->setBody('block-1-body');
        $manager->persist($block);

        $block = new SimpleBlock();
        $block->setParentDocument($parent);
        $block->setName('block-2');
        $block->setTitle('block-2-title');
        $block->setBody('block-2-body');
        $block->setPublishable(false);
        $manager->persist($block);

        //Action
        $actionBlockOne = new ActionBlock();
        $actionBlockOne->setParentDocument($parent);
        $actionBlockOne->setName('action-block-1');
        $actionBlockOne->setActionName('cmf_block_test.test_controller:dummyAction');
        $actionBlockOne->setPublishable(true);
        $manager->persist($actionBlockOne);

        $actionBlockTwo = new ActionBlock();
        $actionBlockTwo->setParentDocument($parent);
        $actionBlockTwo->setName('action-block-2');
        $actionBlockTwo->setActionName('FooBundle:Bar:actionTwo');
        $actionBlockTwo->setPublishable(false);
        $manager->persist($actionBlockTwo);

        //Container
        $childBlockOne = new SimpleBlock();
        $childBlockOne->setName('block-child-1');
        $childBlockOne->setTitle('block-child-1-title');
        $childBlockOne->setBody('block-child-1-body');

        $containerBlock = new ContainerBlock();
        $containerBlock->setParentDocument($parent);
        $containerBlock->setName('container-block-1');
        $containerBlock->addChild($childBlockOne);
        $manager->persist($containerBlock);

        $block = new ContainerBlock();
        $block->setParentDocument($parent);
        $block->setName('container-block-2');
        $block->setPublishable(false);
        $manager->persist($block);

        //Reference
        $block = new ReferenceBlock();
        $block->setParentDocument($parent);
        $block->setName('reference-block-1');
        $block->setReferencedBlock($actionBlockOne);
        $manager->persist($block);

        $block = new ReferenceBlock();
        $block->setParentDocument($parent);
        $block->setName('reference-block-2');
        $block->setReferencedBlock($actionBlockTwo);
        $block->setPublishable(false);
        $manager->persist($block);

        // Menu Nodes
        NodeHelper::createPath($manager->getPhpcrSession(), '/test/menus');
        $menuRoot = $manager->find(null, '/test/menus');
        $menu = new Menu();
        $menu->setName('test-menu');
        $menu->setLabel('Test Menu');
        $menu->setParentDocument($menuRoot);
        $manager->persist($menu);

        $menuNodeOne = new MenuNode();
        $menuNodeOne->setName('menu-node-1');
        $menuNodeOne->setLabel('menu-node-1');
        $menuNodeOne->setParentDocument($menu);
        $manager->persist($menuNodeOne);

        $menuNodeTwo = new MenuNode();
        $menuNodeTwo->setName('menu-node-2');
        $menuNodeTwo->setLabel('menu-node-2');
        $menuNodeTwo->setParentDocument($menu);
        $manager->persist($menuNodeTwo);

        //Menu
        $block = new MenuBlock();
        $block->setParentDocument($parent);
        $block->setName('menu-block-1');
        $block->setMenuNode($menuNodeOne);
        $manager->persist($block);

        $block = new MenuBlock();
        $block->setParentDocument($parent);
        $block->setName('menu-block-2');
        $block->setMenuNode($menuNodeTwo);
        $block->setPublishable(false);
        $manager->persist($block);

        //String
        $block = new StringBlock();
        $block->setParentDocument($parent);
        $block->setName('string-block-1');
        $block->setBody('string-block-1-body');
        $manager->persist($block);

        $block = new StringBlock();
        $block->setParentDocument($parent);
        $block->setName('string-block-2');
        $block->setBody('string-block-2-body');
        $block->setPublishable(false);
        $manager->persist($block);

        $manager->flush();
    }
}
