<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Admin;

use Sonata\DoctrinePHPCRAdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class SlideshowAdmin extends Admin
{

    protected function configureListFields(ListMapper $listMapper)
    {
        parent::configureListFields($listMapper);
        $listMapper
            ->addIdentifier('path', 'text')
            ->add('title', 'text');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);
        $formMapper
            ->with('form.group_general')
                ->add('title', 'text')
            ->with('Items')
                ->add('children', 'sonata_type_collection',
                array(
                    'by_reference' => false
                ),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    'admin_code' => 'symfony_cmf_block.slideshow_item_admin'
                ))
            ->end();
    }

    // TODO: Deletion doesn't work yet

}
