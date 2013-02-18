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
    public function getDefaultActionName()
    {
        return 'symfony_cmf.block.rss_controller:block';
    }

    public function getDefaultSettings()
    {
        return array(
            'url'       => false,
            'title'     => 'Insert the rss title',
            'maxItems'  => 10,
            'template'  => 'SymfonyCmfBlockBundle:Block:block_rss.html.twig',
            'itemClass' => 'Symfony\Cmf\Bundle\BlockBundle\Model\FeedItem',
        );
    }
}
