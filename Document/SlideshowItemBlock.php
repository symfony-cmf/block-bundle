<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Symfony\Cmf\Bundle\BlockBundle\Document\BaseBlock;

/**
 * Block that acts as an item of a SlideshowBlock
 *
 * @PHPCRODM\Document(referenceable=true)
 */
class SlideshowItemBlock extends BaseBlock
{

    /**
     * @PHPCRODM\Child(name="image", cascade="persist")
     */
    protected $image;

    /** @PHPCRODM\String */
    protected $label;

    /** @PHPCRODM\Node */
    protected $node;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'symfony_cmf.block.slideshow_item';
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setImage($image)
    {
        if (!$image) {
            return;
        } elseif ($this->image && $this->image->getFile()) {
            // TODO: this is needed due to a bug in PHPCRODM (http://www.doctrine-project.org/jira/browse/PHPCR-98)
            // TODO: this can be removed once the bug is fixed
            $this->image->getFile()->setFileContent($image->getFile()->getFileContent());
        } else {
            $this->image = $image;
        }
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getNode()
    {
        return $this->node;
    }

}
