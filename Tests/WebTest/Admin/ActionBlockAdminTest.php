<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\WebTest\Admin;

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
            '/admin/cmf/block/actionblock/list',
            array('action-block-1', 'cmf_block_test.test_controller:dummyAction', 'action-block-2')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockEdit()
    {
        $this->makeEditAssertions(
            '/admin/cmf/block/actionblock/test/blocks/action-block-1/edit',
            array('action-block-1', 'cmf_block_test.test_controller:dummyAction')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockCreate()
    {
        $this->makeCreateAssertions(
            '/admin/cmf/block/actionblock/create',
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
        $this->makeDeleteAssertions('/admin/cmf/block/actionblock/test/blocks/action-block-1/delete');
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockShow()
    {
        $this->makeShowAssertions(
            '/admin/cmf/block/actionblock/test/blocks/action-block-1/show',
            array('action-block-1')
        );
    }
}
