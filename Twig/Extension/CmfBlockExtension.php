<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Twig\Extension;

use Symfony\Cmf\Bundle\BlockBundle\Templating\Helper\CmfBlockHelper;

/**
 * Utility function for blocks.
 *
 * @author David Buchmann <david@liip.ch>
 */
class CmfBlockExtension extends \Twig_Extension
{
    protected $blockHelper;

    public function __construct(CmfBlockHelper $blockHelper)
    {
        $this->blockHelper = $blockHelper;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter(
                'cmf_embed_blocks',
                array($this->blockHelper, 'embedBlocks'),
                array('is_safe' => array('html'))
            ),
        );
    }

    public function getName()
    {
        return 'cmf_block';
    }
}
