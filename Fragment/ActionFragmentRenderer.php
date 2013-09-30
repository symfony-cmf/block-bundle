<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2013 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\BlockBundle\Fragment;

use Symfony\Component\HttpKernel\Fragment\InlineFragmentRenderer;

/**
 * Implements the inline rendering strategy where the Request is rendered by
 * the current HTTP kernel.
 *
 * Use a different fragmentPath to prevent loosing objects in the Request
 * attributes. This happens in the FragmentListerner when the request
 * attributes are fixed.
 */
class ActionFragmentRenderer extends InlineFragmentRenderer
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'cmf_block_action';
    }
}
