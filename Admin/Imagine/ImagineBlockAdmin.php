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
use Symfony\Cmf\Bundle\MediaBundle\Form\Type\ImageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Sonata\DoctrinePHPCRAdminBundle\Form\Type\TreeModelType;

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
                        TreeModelType::class,
                        array('root_node' => $this->getRootPath(), 'choice_list' => array(), 'select_root_node' => true)
                    )
                    ->add('name', TextType::class)
                ->end();
        }

        $formMapper
            ->with('form.group_general')
                ->add('label', TextType::class, array('required' => false))
                ->add('linkUrl', TextType::class, array('required' => false))
                ->add('filter', TextType::class, array('required' => false))
                ->add('image', ImageType::class, array('required' => $imageRequired))
                ->add('position', HiddenType::class, array('mapped' => false))
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
