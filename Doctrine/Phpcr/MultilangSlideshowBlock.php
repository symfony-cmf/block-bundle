<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr;

/**
 * Adding translation to the slideshow block
 */
class MultilangSlideshowBlock extends SlideshowBlock
{
    /**
     * @var string
     */
    protected $locale;

    /*
     * @var string
     */
    protected $title;

    /**
     * Set the locale. When doing a flush, this will have the translated
     * fields be stored as that locale.
     *
     * @param string $locale the locale to use for this block
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Get locale used for this block
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
}
