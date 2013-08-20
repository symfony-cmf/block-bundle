<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\WebTest\Render;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

/**
 * @author Nicolas Bastien <nbastien@prestaconcept.net>
 */
class ActionBlockRenderTest extends BaseTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->db('PHPCR')->loadFixtures(array(
            'Symfony\Cmf\Bundle\BlockBundle\Tests\Resources\DataFixtures\Phpcr\LoadBlockData',
        ));
        $this->client = $this->createClient();
    }

    public function testRenderActionTwig()
    {
        $crawler = $this->client->request('GET', '/render-action-test');

        $res = $this->client->getResponse();
        $this->assertEquals(500, $res->getStatusCode());

        $this->assertCount(1, $crawler->filter('html:contains("Bundle "FooBundle" does not exist or it is not enabled")'));
    }
}
