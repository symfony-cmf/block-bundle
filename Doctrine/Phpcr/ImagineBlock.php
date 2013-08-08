<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr;

use Doctrine\ODM\PHPCR\Document\Image;
use Symfony\Cmf\Bundle\CoreBundle\Translatable\TranslatableInterface;

/**
 * Block to hold an image
 */
class ImagineBlock extends AbstractBlock implements TranslatableInterface
{
    /**
     * @var Image
     */
    protected $image;

    /**
     * @var string
     */
    protected $label;

    /**
     * Optional link url to use on the image
     *
     * @var string
     */
    protected $linkUrl;

    /**
     * @var string
     */
    protected $filter;

    /**
     * @var \PHPCR\NodeInterface
     */
    protected $node;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'cmf.block.imagine';
    }

    /**
     * Set label
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set link url
     *
     * @param string $url
     */
    public function setLinkUrl($url)
    {
        $this->linkUrl = $url;
    }

    /**
     * Get link url
     *
     * @return string
     */
    public function getLinkUrl()
    {
        return $this->linkUrl;
    }

    /**
     * Sets the Imagine filter which is going to be used
     *
     * @param string $filter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    /**
     * Get the Imagine filter
     *
     * @return string
     */
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
     * Get image
     *
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Get node
     *
     * @return \PHPCR\NodeInterface
     */
    public function getNode()
    {
        return $this->node;
    }
}
