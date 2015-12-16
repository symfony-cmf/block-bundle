<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr;

use Sonata\BlockBundle\Model\BlockInterface;

/**
 * Block that is a reference to another block.
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
     * @return $this
     */
    public function setReferencedBlock(BlockInterface $referencedBlock)
    {
        $this->referencedBlock = $referencedBlock;

        return $this;
    }
}
