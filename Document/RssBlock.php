<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Symfony\Cmf\Bundle\BlockBundle\Document\BaseBlock;

/**
 * Rss Block
 *
 * @PHPCRODM\Document(referenceable=true)
 */
class RssBlock extends BaseBlock
{
    public function getType()
    {
        return 'symfony_cmf.block.rss';
    }
}
