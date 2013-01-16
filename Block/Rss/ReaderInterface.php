<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Block\Rss;

use Sonata\BlockBundle\Model\BlockInterface;

interface ReaderInterface
{
    /**
     * Fetch a feed providing a url
     *
     * @param \Sonata\BlockBundle\Model\BlockInterface
     * @return mixed array with feed data, FALSE if not found
     */
    function import(BlockInterface $block);
}