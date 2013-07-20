<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr;

/**
 * Block to display a list of rss items
 */
class RssBlock extends ActionBlock
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'cmf.block.action';
    }

    /**
     * Returns the default action name
     *
     * @return string
     */
    public function getDefaultActionName()
    {
        return 'cmf.block.rss_controller:block';
    }
}
