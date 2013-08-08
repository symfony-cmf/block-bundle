<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\WebTest;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

class BlockAdminTest extends BaseTestCase
{
    public function setUp()
    {
        $this->db('PHPCR')->loadFixtures(array(
            'Symfony\Cmf\Bundle\BlockBundle\Tests\Resources\DataFixtures\Phpcr\LoadBlockData',
        ));
        $this->client = $this->createClient();
    }

    public function testBlockList()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/block/simple/list');
        $res = $this->client->getResponse();
        $this->assertEquals(200, $res->getStatusCode());
        $this->assertCount(1, $crawler->filter('html:contains("block-1")'));
    }

    public function testBlockEdit()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/block/simple/test/blocks/block-1/edit');
        $res = $this->client->getResponse();
        $this->assertEquals(200, $res->getStatusCode());
        $this->assertCount(1, $crawler->filter('input[value="block-1"]'));
    }

    public function testBlockCreate()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/block/simple/create');
        $res = $this->client->getResponse();
        $this->assertEquals(200, $res->getStatusCode());

        $button = $crawler->selectButton('Create');
        $form = $button->form();
        $node = $form->getFormNode();
        $actionUrl = $node->getAttribute('action');
        $uniqId = substr(strchr($actionUrl, '='), 1);

        $form[$uniqId.'[parentDocument]'] = '/test/blocks';
        $form[$uniqId.'[name]'] = 'foo-test';
        $form[$uniqId.'[title]'] = 'Foo Test';
        $form[$uniqId.'[body]'] = 'Block body foo bar.';

        $this->client->submit($form);
        $res = $this->client->getResponse();

        // If we have a 302 redirect, then all is well
        $this->assertEquals(302, $res->getStatusCode());
    }
}
