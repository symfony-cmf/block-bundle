<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

use Symfony\Cmf\Bundle\BlockBundle\Document\MultilangSimpleBlock;

class MultilangSimpleBlockAdmin extends SimpleBlockAdmin
{
    protected $translationDomain = 'SymfonyCmfBlockBundle';

    /**
     * @var array
     */
    protected $locales;

    /**
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param array  $locales
     */
    public function __construct($code, $class, $baseControllerName, $locales)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->locales = $locales;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        parent::configureListFields($listMapper);
        $listMapper
            ->add('locales', 'text', array('template' => 'SonataDoctrinePHPCRAdminBundle:CRUD:locales.html.twig'))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form.group_general')
                ->add('locale', 'choice', array(
                    'choices' => array_combine($this->locales, $this->locales),
                    'empty_value' => '',
                ))
            ->end()
        ;

        parent::configureFormFields($formMapper);
    }

    public function getNewInstance()
    {
        /** @var $new MultilangSimpleBlock */
        $new = parent::getNewInstance();

        if ($this->hasRequest()) {
            $currentLocale = $this->getRequest()->attributes->get('_locale');

            if (in_array($currentLocale, $this->locales)) {
                $meta = $this->getModelManager()->getMetadata(get_class($new));
                $meta->setFieldValue($new, $meta->localeMapping, $currentLocale);
            }
        }

        return $new;
    }
}
