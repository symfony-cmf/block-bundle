<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Admin;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ODM\PHPCR\ChildrenCollection;
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
                    'by_reference' => false,
                ),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    'admin_code' => 'symfony_cmf_block.slideshow_item_admin',
                    'sortable'  => 'position',
                ))
            ->end();

        $formBuilder = $formMapper->getFormBuilder();
        $formBuilder->addEventListener(FormEvents::POST_BIND, array($this, 'onPostBind'));

    }

    // reorder children according to the form data
    public function onPostBind(FormEvent $event)
    {
        /** @var $newCollection ChildrenCollection */
        $newCollection = $event->getData()->getChildren();
        $newCollection->clear();

        foreach ($event->getForm()->get('children') as $child) {
            $newCollection->add($child->getData());
        }
    }

    // TODO: Deletion doesn't work yet

}
