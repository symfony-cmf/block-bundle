<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sonata\BlockBundle\Block\BlockServiceInterface;
use Sonata\BlockBundle\Block\BaseBlockService as SonataBaseBlockService;

abstract class BaseBlockService extends SonataBaseBlockService implements BlockServiceInterface
{
    /**
     * Sets the default options for this block.
     *
     * @param OptionsResolverInterface $resolver The resolver for the options.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'attr'     => array(),
        ));

        $resolver->setAllowedTypes(array(
            'attr'     => 'array',
        ));
    }
}
