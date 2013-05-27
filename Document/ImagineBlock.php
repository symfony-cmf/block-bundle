<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Doctrine\ODM\PHPCR\Document\Image;
use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

use Symfony\Cmf\Bundle\BlockBundle\Document\BaseBlock;

/**
 * Block to hold an image
 *
 * @PHPCRODM\Document(referenceable=true)
 */
class ImagineBlock extends BaseBlock
{
    /**
     * @var Image
     * @PHPCRODM\Child(cascade="persist")
     */
    protected $image;

    /** @PHPCRODM\String */
    protected $label;

    /**
     * Optional link url to use on the image
     * @PHPCRODM\String
     */
    protected $linkUrl;

    /** @PHPCRODM\String */
    protected $filter;

    /**
     * @var \PHPCR\NodeInterface
     * @PHPCRODM\Node
     */
    protected $node;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'cmf.block.imagine';
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLinkUrl($url)
    {
        $this->linkUrl = $url;
    }

    public function getLinkUrl()
    {
        return $this->linkUrl;
    }

    /**
     * Sets the Imagine filter which is going to be used
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Set the image for this block.
     *
     * Setting null will do nothing, as this is what happens when you edit this
     * block in a form without uploading a replacement file.
     *
     * If you need to delete the Image, you can use getImage and delete it with
     * the document manager. Note that this block does not make much sense
     * without an image, though.
     *
     * @param Image $image optional the image to update
     */
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

    /**
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return \PHPCR\NodeInterface
     */
    public function getNode()
    {
        return $this->node;
    }

}
