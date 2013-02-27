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

    /** @PHPCRODM\String */
    protected $title;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'symfony_cmf.block.slideshow';
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

}
