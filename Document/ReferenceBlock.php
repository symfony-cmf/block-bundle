<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

use Sonata\BlockBundle\Model\BlockInterface;

/**
 * Block that is a reference to another block
 *
 * @PHPCRODM\Document(referenceable=true)
 */
class ReferenceBlock extends BaseBlock
{
    /** @PHPCRODM\ReferenceOne */
    private $referencedBlock;

    public function getType()
    {
        return 'cmf.block.reference';
    }

    /**
     * @return BlockInterface|null
     */
    public function getReferencedBlock()
    {
        return $this->referencedBlock;
    }

    /**
     * @param BlockInterface $referencedBlock
     *
     * @return ReferenceBlock itself
     */
    public function setReferencedBlock(BlockInterface $referencedBlock)
    {
        $this->referencedBlock = $referencedBlock;

        return $this;
    }
}
