<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RssBlockService extends ActionBlockService
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        parent::setDefaultSettings($resolver);

        $resolver->setDefaults(array(
            'url'       => false,
            'title'     => 'Insert the rss title',
            'maxItems'  => 10,
            'template'  => 'SymfonyCmfBlockBundle:Block:block_rss.html.twig',
            'itemClass' => 'Symfony\Cmf\Bundle\BlockBundle\Model\FeedItem',
        ));
    }
}
