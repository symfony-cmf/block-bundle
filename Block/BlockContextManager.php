<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Doctrine\Common\Util\ClassUtils;
use Sonata\BlockBundle\Block\BlockContextManager as BaseBlockContextManager;
use Sonata\BlockBundle\Model\BlockInterface;

class BlockContextManager extends BaseBlockContextManager
{
    /**
     * {@inheritdoc}
     */
    public function getClass(BlockInterface $block)
    {
        return ClassUtils::getClass($block);
    }
}