<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr;

use Sonata\BlockBundle\Model\BlockInterface;

/**
 * Block that is a reference to another block
 */
class ReferenceBlock extends AbstractBlock
{
    /**
     * @var BlockInterface
     */
    private $referencedBlock;

    /**
     * {@inheritdoc}
     */
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
