<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Resources\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ActionBlock;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SimpleBlock;
use Doctrine\ODM\PHPCR\Document\Generic;

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

        $block = new SimpleBlock();
        $block->setParentDocument($parent);
        $block->setName('block-1');
        $block->setTitle('block-1-title');
        $block->setBody('block-1-body');
        $manager->persist($block);

        $block = new SimpleBlock();
        $block->setParentDocument($parent);
        $block->setName('block-2');
        $block->setPublishable(false);
        $manager->persist($block);

        $block = new ActionBlock();
        $block->setParentDocument($parent);
        $block->setName('action-block-1');
        $block->setActionName('FooBundle:Bar:actionOne');
        $block->setPublishable(true);
        $manager->persist($block);

        $block = new ActionBlock();
        $block->setParentDocument($parent);
        $block->setName('action-block-2');
        $block->setActionName('FooBundle:Bar:actionTwo');
        $block->setPublishable(false);
        $manager->persist($block);

        $manager->flush();
    }
}
