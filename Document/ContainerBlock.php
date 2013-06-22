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
     * @var ChildrenCollection
     * @PHPCRODM\Children(cascade={"all"})
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
        return 'cmf.block.container';
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
     * @param BlockInterface $child
     * @param string         $key   the collection index name to use in the
     *      child collection. if not set, the child will simply be appended at
     *      the end
     *
     * @return boolean
     */
    public function addChild(BlockInterface $child, $key = null)
    {
        if ($key != null) {

            $this->children->set($key, $child);
            return true;
        }

        return $this->children->add($child);
    }

    /**
     * Alias to addChild to make the form layer happy
     *
     * @param BlockInterface $children
     *
     * @return boolean
     */
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
