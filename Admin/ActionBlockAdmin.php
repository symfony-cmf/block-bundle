<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Admin;

use Sonata\DoctrinePHPCRAdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Symfony\Cmf\Bundle\BlockBundle\Document\ActionBlock;

class ActionBlockAdmin extends Admin
{
    protected $translationDomain = 'CmfBlockBundle';

    /**
     * Root path for the route content selection
     * @var string
     */
    protected $contentRoot;

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', 'text')
            ->add('name', 'text')
            ->add('actionName', 'text')
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form.group_general')
            ->add('parentDocument', 'doctrine_phpcr_odm_tree', array('root_node' => $this->contentRoot, 'choice_list' => array(), 'select_root_node' => true))
            ->add('name', 'text')
            ->add('actionName', 'text')
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name',  'doctrine_phpcr_nodename')
        ;
    }

    public function getNewInstance()
    {
        /** @var $new ActionBlock */
        $new = parent::getNewInstance();
        if ($this->hasRequest()) {
            $parentId = $this->getRequest()->query->get('parent');
            if (null !== $parentId) {
                $new->setParentDocument($this->getModelManager()->find(null, $parentId));
            }
        }
        return $new;
    }

    public function setContentRoot($contentRoot)
    {
        $this->contentRoot = $contentRoot;
    }

    public function getExportFormats()
    {
        return array();
    }
}
