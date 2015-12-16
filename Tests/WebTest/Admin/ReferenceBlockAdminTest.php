<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\WebTest\Admin;

/**
 * @author Nicolas Bastien <nbastien@prestaconcept.net>
 */
class ReferenceBlockAdminTest extends AbstractBlockAdminTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testBlockList()
    {
        $this->makeListAssertions(
            '/admin/cmf/block/referenceblock/list',
            array('reference-block-1', 'reference-block-2')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockEdit()
    {
        $this->makeEditAssertions(
            '/admin/cmf/block/referenceblock/test/blocks/reference-block-1/edit',
            array('reference-block-1')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockCreate()
    {
        $this->makeCreateAssertions(
            '/admin/cmf/block/referenceblock/create',
            array(
                'parentDocument' => '/test/blocks',
                'name' => 'foo-test-container',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockDelete()
    {
        $this->makeDeleteAssertions('/admin/cmf/block/referenceblock/test/blocks/reference-block-1/delete');
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockShow()
    {
        $this->makeShowAssertions(
            '/admin/cmf/block/referenceblock/test/blocks/reference-block-1/show',
            array('reference-block-1')
        );
    }
}
