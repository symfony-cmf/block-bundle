<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

/**
 * Block to display a list of rss items
 *
 * @PHPCRODM\Document(referenceable=true)
 */
class RssBlock extends ActionBlock
{
    public function getType()
    {
        return 'cmf.block.action';
    }

    public function getDefaultActionName()
    {
        return 'cmf.block.rss_controller:block';
    }
}
