<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr;

use Symfony\Cmf\Bundle\BlockBundle\Model\AbstractBlock as AbstractBlockModel;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Base for the PHPCR-ODM blocks provided by this bundle.
 *
 * Parent handling: In addition to the model block, this class follows
 * the PHPCR-ODM HierarchyInterface and redirects the ParentDocument methods
 * to the ParentObject methods. It can't implement the interface until the
 * deprecated getParent and setParent methods have been removed, because of
 * the conflict with the BlockInterface signature.
 */
abstract class AbstractBlock extends AbstractBlockModel
{
    /**
     * Alias of setParentObject.
     *
     * {@inheritdoc}
     */
    public function setParentDocument($parent)
    {
        return $this->setParentObject($parent);
    }

    /**
     * Alias of getParentObject.
     *
     * {@inheritdoc}
     */
    public function getParentDocument()
    {
        return $this->getParentObject();
    }

    /**
     * Validate settings.
     *
     * @param ExecutionContextInterface $context
     */
    public function isSettingsValid(ExecutionContextInterface $context)
    {
        foreach ($this->getSettings() as $value) {
            if (is_array($value)) {
                $context->addViolation('settings', 'A multidimensional array is not allowed, only use key-value pairs.');
            }
        }
    }
}
