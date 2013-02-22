<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Doctrine\ODM\PHPCR\ChildrenCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Sonata\BlockBundle\Model\BlockInterface;

/**
 * Block that contains other blocks
 *
 * @PHPCRODM\Document(referenceable=true)
 */
class ContainerBlock extends BaseBlock
{
    /**
     * \Doctrine\Common\Collections\ArrayCollection
     * @PHPCRODM\Children
     */
    protected  $children;

    public function __construct($name = null)
    {
        $this->setName($name);
        $this->children = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'symfony_cmf.block.container';
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren(ChildrenCollection $children)
    {
        return $this->children = $children;
    }

    /**
     * Add a child to this container
     *
     * @param  BlockInterface $child
     * @return boolean
     */
    public function addChild(BlockInterface $child)
    {
        return $this->children->add($child);
    }

    public function addChildren(BlockInterface $children)
    {
        return $this->addChild($children);
    }

    /**
     * Remove a child from this container
     *
     * @param mixed $child
     * @return void
     */
    public function removeChild($child)
    {
        $this->children->removeElement($child);
    }

}
