<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

/**
 * Block that contains only text
 *
 * @PHPCRODM\Document(referenceable=true)
 */
class StringBlock extends BaseBlock
{
    /** 
     * @PHPCRODM\String 
     */ 
    protected $content;

    public function getType()
    {
        return 'cmf.block.string';
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

