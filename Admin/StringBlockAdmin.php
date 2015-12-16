<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * @author Daniel Leech <daniel@dantleech.com>
 */
class StringBlockAdmin extends AbstractBlockAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('id', 'text');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $isSf28 = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix');
        $doctrineTreeType = $isSf28 ? 'Sonata\DoctrinePHPCRAdminBundle\Form\Type\TreeModelType' : 'doctrine_phpcr_odm_tree';
        $textType = $isSf28 ? 'Symfony\Component\Form\Extension\Core\Type\TextType' : 'text';
        $textareaType = $isSf28 ? 'Symfony\Component\Form\Extension\Core\Type\TextareaType' : 'textarea';

        $formMapper
            ->with('form.group_general')
            ->add('parentDocument', $doctrineTreeType, array('root_node' => $this->getRootPath(), 'choice_list' => array(), 'select_root_node' => true))
            ->add('name', $textType)
            ->add('body', $textareaType)
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name', 'doctrine_phpcr_nodename');
    }
}
