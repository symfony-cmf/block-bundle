<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

/**
 * Block that contains hypertext and a title
 *
 * @PHPCRODM\Document(referenceable=true, translator="attribute")
 */
class MultilangSimpleBlock extends BaseBlock
{
    /** @PHPCRODM\Locale */
    protected $locale;

    /** @PHPCRODM\String(translated=true) */
    protected $title;

    /** @PHPCRODM\String(translated=true) */
    protected $content;

    public function getType()
    {
        return 'cmf.block.simple';
    }

    /**
     * @return string the loaded locale of this menu item
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set the locale this menu item should be. When doing a flush,
     * this will have the translated fields be stored as that locale.
     *
     * @param string $locale the locale to use for this menu item
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }
}
