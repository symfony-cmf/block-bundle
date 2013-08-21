<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\WebTest\Admin;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

/**
 * @author Nicolas Bastien <nbastien@prestaconcept.net>
 */
class ActionBlockAdminTest extends AbstractBlockAdminTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testBlockList()
    {
        $this->makeListAssertions(
            '/admin/cmf/block/action/list',
            array('action-block-1', 'cmf_block_test.test_controller:dummyAction', 'action-block-2')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockEdit()
    {
        $this->makeEditAssertions(
            '/admin/cmf/block/action/test/blocks/action-block-1/edit',
            array('action-block-1', 'cmf_block_test.test_controller:dummyAction')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockCreate()
    {
        $this->makeCreateAssertions(
            '/admin/cmf/block/action/create',
            array(
                'parentDocument' => '/test/blocks',
                'name'           => 'foo-test-action',
                'actionName'     => 'FooTestBunlde:Bar:action',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockDelete()
    {
        $this->makeDeleteAssertions('/admin/cmf/block/action/test/blocks/action-block-1/delete');
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockShow()
    {
        $this->makeShowAssertions(
            '/admin/cmf/block/action/test/blocks/action-block-1/show',
            array('action-block-1')
        );
    }
}
