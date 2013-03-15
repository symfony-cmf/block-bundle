<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Admin\Imagine;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ODM\PHPCR\ChildrenCollection;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\DoctrinePHPCRAdminBundle\Admin\Admin;

class MinimalSlideshowAdmin extends Admin
{

    protected $baseRouteName = 'symfony_cmf_block.imagine.minimal_slideshow_admin';
    protected $baseRoutePattern = 'symfony_cmf/block/imagineMinimalSlideshow';

    protected function configureListFields(ListMapper $listMapper)
    {
        parent::configureListFields($listMapper);
        $listMapper
            ->addIdentifier('id', 'text')
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
                        'admin_code' => 'symfony_cmf_block.imagine.minimal_imagine_admin',
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
            if ($child->get('_delete')->getData()) {
                // do not re-add a deleted child
                continue;
            }
            if ($child->getName()) {
                // keep key in collection
                $newCollection[$child->getName()] = $child->getData();
            } else {
                $newCollection[] = $child->getData();
            }
        }
    }

    public function prePersist($slideshow)
    {
        foreach($slideshow->getChildren() as $child) {
            $child->setName($this->generateName());
        }
    }

    public function preUpdate($slideshow)
    {
        foreach($slideshow->getChildren() as $child) {
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
