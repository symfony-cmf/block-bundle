<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Twig\Extension;

use Symfony\Cmf\Bundle\BlockBundle\Templating\Helper\CmfBlockHelper;
use Twig_Environment;

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
        return [
            new \Twig_SimpleFilter(
                'cmf_embed_blocks',
                [$this->blockHelper, 'embedBlocks'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function getName()
    {
        return 'cmf_block';
    }

    /**
     * Initializes the runtime environment.
     *
     * This is where you can load some file that contains filter functions for instance.
     *
     * @param Twig_Environment $environment The current Twig_Environment instance
     *
     * @deprecated since 1.23 (to be removed in 2.0), implement Twig_Extension_InitRuntimeInterace instead
     */
    public function initRuntime(Twig_Environment $environment): void
    {
        // TODO: Implement initRuntime() method.
    }

    /**
     * Returns a list of global variables to add to the existing list.
     *
     * @return array An array of global variables
     *
     * @deprecated since 1.23 (to be removed in 2.0), implement Twig_Extension_GlobalsInterface instead
     */
    public function getGlobals(): []
    {
        return [];
    }
}
