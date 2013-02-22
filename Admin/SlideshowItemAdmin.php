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
        $imageRequired = $this->getSubject()->getParent() ? false : true;

        $formMapper
            ->with('form.group_general')
                ->add('label', 'text')
                ->add('image', 'phpcr_image', array('required' => $imageRequired, 'label' => 'Slide Image', 'data_class' => 'Doctrine\ODM\PHPCR\Document\Image'))
                ->add('position', 'hidden', array('mapped' => false))
            ->end();

    }

}
