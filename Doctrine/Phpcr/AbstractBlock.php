<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2013 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr;

use Symfony\Cmf\Bundle\BlockBundle\Model\AbstractBlock as AbstractBlockModel;
use Symfony\Component\Validator\ExecutionContextInterface;

abstract class AbstractBlock extends AbstractBlockModel
{
    /**
     * Validate settings
     *
     * @param \Symfony\Component\Validator\ExecutionContext $context
     */
    public function isSettingsValid(ExecutionContextInterface $context)
    {
        foreach ($this->getSettings() as $value) {
            if (is_array($value)) {
                $context->addViolationAt('settings', 'A multidimensional array is not allowed, only use key-value pairs.');
            }
        }
    }
}
