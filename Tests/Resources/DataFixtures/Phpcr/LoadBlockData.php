<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Resources\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SimpleBlock;
use Doctrine\ODM\PHPCR\Document\Generic;

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
        $manager->persist($block);

        $block = new SimpleBlock();
        $block->setParentDocument($parent);
        $block->setName('block-2');
        $block->setPublishable(false);
        $manager->persist($block);

        $manager->flush();
    }
}
