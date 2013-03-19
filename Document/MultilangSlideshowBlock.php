<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

/**
 * Adding translation to the slideshow block
 *
 * @PHPCRODM\Document(referenceable=true, translator="attribute")
 */
class MultilangSlideshowBlock extends SlideshowBlock
{
    /** @PHPCRODM\Locale */
    protected $locale;

    /** @PHPCRODM\String(translated=true) */
    protected $title;

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }
}
