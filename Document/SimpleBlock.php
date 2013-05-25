<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

/**
 * Block that contains hypertext and a title
 *
 * @PHPCRODM\Document(referenceable=true)
 */
class SimpleBlock extends BaseBlock
{
    /** @PHPCRODM\String */
    protected $title;

    /** @PHPCRODM\String */
    protected $content;

    public function getType()
    {
        return 'cmf.block.simple';
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
