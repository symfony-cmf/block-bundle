<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Twig\Extension;

use Symfony\Cmf\Bundle\BlockBundle\Templating\Helper\CmfBlockHelper;

use Sonata\BlockBundle\Twig\Extension\BlockExtension as SonataBlockExtension;

/**
 * Utility function for blocks
 *
 * @author David Buchmann <david@liip.ch>
 */
class CmfBlockExtension extends SonataBlockExtension
{
    public function __construct(CmfBlockHelper $blockHelper)
    {
        $this->blockHelper = $blockHelper;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFunction('cmf_embed_blocks',
                array($this->blockHelper, 'cmfEmbedBlocks'),
                array('is_safe' => array('html'))
            ),
        );
    }

    public function getName()
    {
        return 'cmf_block';
    }
}
