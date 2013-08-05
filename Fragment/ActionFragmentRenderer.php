<?php

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
