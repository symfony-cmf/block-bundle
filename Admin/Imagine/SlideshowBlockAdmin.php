<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Admin\Imagine;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Symfony\Cmf\Bundle\BlockBundle\Admin\AbstractBlockAdmin;

/**
 * @author Horner
 */
class SlideshowBlockAdmin extends AbstractBlockAdmin
{
    protected $baseRouteName = 'cmf_block_slideshow';
    protected $baseRoutePattern = '/cmf/block/slideshow';

    /**
     * Path to where new slideshow blocks may be attached
     *
     * @var string
     */
    protected $blockRoot;

    /**
     * Service name of the sonata_type_collection service to embed
     *
     * @var string
     */
    protected $embeddedAdminCode;

    /**
     * @param string $blockRoot
     */
    public function setBlockRoot($blockRoot)
    {
        $this->blockRoot = $blockRoot;
    }

    /**
     * Configure the service name (admin_code) of the admin service for the embedded slides
     *
     * @param string $adminCode
     */
    public function setEmbeddedSlidesAdmin($adminCode)
    {
        $this->embeddedAdminCode = $adminCode;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        parent::configureListFields($listMapper);
        $listMapper
            ->addIdentifier('id', 'text')
            ->add('title', 'text');
    }

    /**
     * {@inheritdoc}
     */
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
                        'admin_code' => $this->embeddedAdminCode,
                        'sortable'  => 'position',
                    ))
            ->end();

        if (null === $this->getParentFieldDescription()) {
            $formMapper
                ->with('form.group_general')
                    ->add('parentDocument', 'doctrine_phpcr_odm_tree', array('root_node' => $this->blockRoot, 'choice_list' => array(), 'select_root_node' => true))
                    ->add('name', 'text')
                ->end()
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($slideshow)
    {
        foreach ($slideshow->getChildren() as $child) {
            $child->setName($this->generateName());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($slideshow)
    {
        foreach ($slideshow->getChildren() as $child) {
            if (! $this->modelManager->getNormalizedIdentifier($child)) {
                $child->setName($this->generateName());
            }
        }
    }

    /**
     * Generate a most likely unique name
     *
     * TODO: have child documents use the autoname annotation once this is done: http://www.doctrine-project.org/jira/browse/PHPCR-103
     *
     * @return string
     */
    private function generateName()
    {
        return 'child_' . time() . '_' . rand();
    }
}
