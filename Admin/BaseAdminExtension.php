<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Admin;

use Sonata\AdminBundle\Admin\AdminExtension;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Class BaseAdminExtension
 * @package Symfony\Cmf\Bundle\BlockBundle\Admin
 * @author Sven Cludius<sven.cludius@valiton.com>
 */
class BaseAdminExtension extends AdminExtension
{
    /**
     * Configure form fields
     *
     * @param FormMapper $formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form.group_base')
                ->add('enabled', 'checkbox')
                ->add('ttl', 'text')
            ->end()
        ;
    }

}
