<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Admin\Imagine;

use Symfony\Cmf\Bundle\BlockBundle\Admin\AbstractBlockAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ImagineBlock;

/**
 * @author Horner
 */
class ImagineBlockAdmin extends AbstractBlockAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        parent::configureListFields($listMapper);
        $listMapper
            ->addIdentifier('id', 'text')
            ->add('name', 'text')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);

        // image is only required when creating a new item
        // TODO: sonata is not using one admin instance per object, so this doesn't really work - https://github.com/symfony-cmf/BlockBundle/issues/151
        $imageRequired = ($this->getSubject() && $this->getSubject()->getParentDocument()) ? false : true;

        if (null === $this->getParentFieldDescription()) {
            $formMapper
                ->with('form.group_general')
                ->add(
                    'parentDocument',
                    'doctrine_phpcr_odm_tree',
                    array('root_node' => $this->getRootPath(), 'choice_list' => array(), 'select_root_node' => true)
                )
                ->add('name', 'text')
            ->end();
        }

        $formMapper
            ->with('form.group_general')
                ->add('label', 'text', array('required' => false))
                ->add('linkUrl', 'text', array('required' => false))
                ->add('filter', 'text', array('required' => false))
                ->add('image', 'cmf_media_image', array('required' => $imageRequired))
                ->add('position', 'hidden', array('mapped' => false))
            ->end();
    }

    public function toString($object)
    {
        return $object instanceof ImagineBlock && $object->getLabel()
            ? $object->getLabel()
            : parent::toString($object)
        ;
    }
}
