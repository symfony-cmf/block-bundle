<?php
/*
 * This file is part of the Symfony CMF package.
 *
 * (c) Symfony2 CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
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
