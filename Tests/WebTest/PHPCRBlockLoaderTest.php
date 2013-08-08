<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\WebTest;

use Symfony\Cmf\Bundle\BlockBundle\Block\PhpcrBlockLoader;
use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class PHPCRBlockLoaderTest extends BaseTestCase
{
    /**
     * @var Client
     */
    private $client;

    public function setUp()
    {
        $this->db('PHPCR')->loadFixtures(array(
            'Symfony\Cmf\Bundle\BlockBundle\Tests\Resources\DataFixtures\Phpcr\LoadBlockData',
        ));
        $this->client = $this->createClient();
    }

    public function testGetUnpublished()
    {
        /** @var $service PhpcrBlockLoader */
        $service = $this->client->getContainer()->get('cmf.block.service');
        $this->assertInstanceOf('Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SimpleBlock', $service->load(array('name' => '/test/blocks/block-1')));
        // this block is not published, should be empty
        $this->assertInstanceOf('Sonata\BlockBundle\Model\EmptyBlock', $service->load(array('name' => '/test/blocks/block-2')));
    }
}
