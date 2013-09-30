<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2013 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Resources\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ActionBlock;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ContainerBlock;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ReferenceBlock;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SimpleBlock;
use Doctrine\ODM\PHPCR\Document\Generic;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\StringBlock;

/**
 * @author David Buchmann <david@liip.ch>
 */
class LoadBlockData implements FixtureInterface, DependentFixtureInterface
{
    public function getDependencies()
    {
        return array(
            'Symfony\Cmf\Component\Testing\DataFixtures\PHPCR\LoadBaseData',
        );
    }

    public function load(ObjectManager $manager)
    {
        $root = $manager->find(null, '/test');
        $parent = new Generic;
        $parent->setParent($root);
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
