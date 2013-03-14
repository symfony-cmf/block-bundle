<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Admin;

use Sonata\DoctrinePHPCRAdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class SlideshowItemAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);

        // image is only required when creating a new item
        // TODO: sonata is not using one admin instance per object, so this doesnt really work - fix it
        $imageRequired = ($this->getSubject() && $this->getSubject()->getParent()) ? false : true;

        $formMapper
            ->with('form.group_general')
                ->add('label', 'text', array('required' => false))
                ->add('image', 'phpcr_odm_image', array('required' => $imageRequired, 'label' => 'Slide Image', 'data_class' => 'Doctrine\ODM\PHPCR\Document\Image'))
                ->add('position', 'hidden', array('mapped' => false))
            ->end();
    }
}
