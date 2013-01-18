<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Doctrine\ODM\PHPCR\ChildrenCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Sonata\BlockBundle\Model\BlockInterface;

/**
 * Block that contains other blocks ...
 *
 * @PHPCRODM\Document(referenceable=true)
 */
class ContainerBlock extends BaseBlock
{
    /** @PHPCRODM\Children */
    protected  $children;

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
     * @param  string $key OPTIONAL
     * @return boolean
     */
    public function addChild(BlockInterface $child, $key = null)
    {
        if (null === $this->children) {
            $this->children = new ArrayCollection();
        }
        if ($key != null) {
            return $this->children->set($key, $child);
        }

        return $this->children->add($child);
    }
}
