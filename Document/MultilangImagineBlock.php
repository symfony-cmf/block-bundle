<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

/**
 * Block to hold an image
 *
 * @PHPCRODM\Document(referenceable=true, translator="attribute")
 */
class MultilangImagineBlock extends ImagineBlock
{

    /** @PHPCRODM\Locale */
    protected $locale;

    /** @PHPCRODM\String(translated=true) */
    protected $label;

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }
}
