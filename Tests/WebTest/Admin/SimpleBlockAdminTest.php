<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\WebTest\Admin;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

/**
 * @author David Buchmann <david@liip.ch>
 */
class SimpleBlockAdminTest extends AbstractBlockAdminTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testBlockList()
    {
        $this->makeListAssertions(
            '/admin/cmf/block/simple/list',
            array('block-1', 'block-1-title', 'block-2')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockEdit()
    {
        $this->makeEditAssertions(
            '/admin/cmf/block/simple/test/blocks/block-1/edit',
            array('block-1', 'block-1-title')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockCreate()
    {
        $this->makeCreateAssertions(
            '/admin/cmf/block/simple/create',
            array(
                'parentDocument' => '/test/blocks',
                'name'           => 'foo-test',
                'title'          => 'Foo Test',
                'body'           => 'Block body foo bar.',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockDelete()
    {
        $this->makeDeleteAssertions('/admin/cmf/block/simple/test/blocks/block-1/delete');
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockShow()
    {
        $this->makeShowAssertions(
            '/admin/cmf/block/simple/test/blocks/block-1/show',
            array('block-1')
        );
    }
}
