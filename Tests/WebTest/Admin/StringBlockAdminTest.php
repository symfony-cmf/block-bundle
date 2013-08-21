<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\WebTest\Admin;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

/**
 * @author Nicolas Bastien <nbastien@prestaconcept.net>
 */
class StringBlockAdminTest extends AbstractBlockAdminTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testBlockList()
    {
        $this->makeListAssertions(
            '/admin/cmf/block/string/list',
            array('string-block-1', 'string-block-2')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockEdit()
    {
        $this->makeEditAssertions(
            '/admin/cmf/block/string/test/blocks/string-block-1/edit',
            array('string-block-1')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockCreate()
    {
        $this->makeCreateAssertions(
            '/admin/cmf/block/string/create',
            array(
                'parentDocument' => '/test/blocks',
                'name'           => 'foo-test-container',
                'body'           => 'string-block-1-body',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockDelete()
    {
        $this->makeDeleteAssertions('/admin/cmf/block/string/test/blocks/string-block-1/delete');
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockShow()
    {
        $this->makeShowAssertions(
            '/admin/cmf/block/string/test/blocks/string-block-1/show',
            array('string-block-1')
        );
    }
}
