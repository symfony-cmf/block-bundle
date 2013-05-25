<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Symfony\Cmf\Bundle\BlockBundle\Document\ContainerBlock;

/**
 * Special container block that renders child items in a way suitable for a
 * slideshow. Note that you need to add some javascript to actually get the
 * blocks to do a slideshow.
 *
 * @PHPCRODM\Document(referenceable=true)
 */
class SlideshowBlock extends ContainerBlock
{

    /** @PHPCRODM\String */
    protected $title;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'cmf.block.slideshow';
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

}
