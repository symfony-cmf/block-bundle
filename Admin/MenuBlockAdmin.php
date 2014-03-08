<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2013 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\BlockBundle\Admin;

use Sonata\DoctrinePHPCRAdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * @author Philipp A. Mohrenweiser <phiamo@googlemail.com>
 */
class MenuBlockAdmin extends Admin
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
                ->add('parentDocument', 'doctrine_phpcr_odm_tree', array('menu_node' => $this->getMenuPath(), 'choice_list' => array(), 'select_menu_node' => true))
                ->add('name', 'text')
                ->add('menuNode', 'doctrine_phpcr_odm_tree', array('choice_list' => array(), 'required' => true, 'menu_node' => $this->menuPath))
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
     * Path to the menu node in the repository under which documents of this
     * admin should be created.
     *
     * @var string
     */
    private $menuPath;

    /**
     * Set the menu path in the repository. To be able to create new items,
     * this path must already exist.
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
