<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Admin\Extension;

use Sonata\AdminBundle\Admin\AdminExtension;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Provide cache form fields for block models
 *
 * @author Sven Cludius <sven.cludius@valiton.com>
 */
class BlockCacheExtension extends AdminExtension
{
    /**
     * Configure form fields
     *
     * @param FormMapper $formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form.group_metadata', array('translation_domain' => 'CmfBlockBundle'))
                ->add('ttl', 'text')
            ->end()
        ;
    }
}
