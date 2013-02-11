<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Symfony\Cmf\Bundle\BlockBundle\Document\ContainerBlock;

/**
 * Block that renders a slideshow of child items
 *
 * @PHPCRODM\Document(referenceable=true)
 */
class SlideshowBlock extends ContainerBlock
{

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'symfony_cmf.block.slideshow';
    }

}
