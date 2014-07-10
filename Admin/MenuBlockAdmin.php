<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Sonata admin for the MenuBlock. Allows to select the target menu node from
 * an odm tree at the menu root.
 *
 * @author Philipp A. Mohrenweiser <phiamo@googlemail.com>
 */
class MenuBlockAdmin extends AbstractBlockAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
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
        $formMapper
            ->with('form.group_general')
                ->add('parentDocument', 'doctrine_phpcr_odm_tree', array('root_node' => $this->getRootPath(), 'choice_list' => array(), 'select_root_node' => true))
                ->add('name', 'text')
                ->add('menuNode', 'doctrine_phpcr_odm_tree', array('choice_list' => array(), 'required' => true, 'root_node' => $this->menuPath))
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name',  'doctrine_phpcr_nodename')
        ;
    }
    /**
     * PHPCR to the root of all menu nodes for the selection of the target.
     *
     * @var string
     */
    private $menuPath;

    /**
     * Set the menu root for selection of the target of this block.
     *
     * @param string $menuPath
     */
    public function setMenuPath($menuPath)
    {
        $this->menuPath = $menuPath;
    }

    /**
     * @return string
     */
    public function getMenuPath()
    {
        return $this->menuPath;
    }
}
