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

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

/**
 * @author Nicolas Bastien <nbastien@prestaconcept.net>
 */
abstract class AbstractBlockAdminTestCase extends BaseTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * Admin listing test case
     */
    abstract public function testBlockList();

    /**
     * Admin edition test case
     */
    abstract public function testBlockEdit();

    /**
     * Admin creation test case
     */
    abstract public function testBlockCreate();

    /**
     * Admin deletion test case
     */
    abstract public function testBlockDelete();

    /**
     * Admin show test case
     */
    abstract public function testBlockShow();

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

    /**
     * Make defaults listing assertions
     *
     * @param $url              string the listing url
     * @param $containsElements array  an array of models identifier which should be in the listing
     */
    protected function makeListAssertions($url, $containsElements)
    {
        $crawler = $this->client->request('GET', $url);
        $res = $this->client->getResponse();
        $this->assertEquals(200, $res->getStatusCode(), $res->getContent());

        foreach ($containsElements as $element) {
            $this->assertCount(1, $crawler->filter('html:contains("' . $element . '")'), $res->getContent());
        }
    }

    /**
     * Make defaults edition assertions
     *
     * @param $url            string the edition url
     * @param $containsValues array  an array of values which should be in the inputs
     */
    protected function makeEditAssertions($url, $containsValues)
    {
        $crawler = $this->client->request('GET', $url);
        $res = $this->client->getResponse();
        $this->assertEquals(200, $res->getStatusCode(), $res->getContent());

        foreach ($containsValues as $value) {
            $this->assertCount(1, $crawler->filter('input[value="' . $value . '"]'), $res->getContent());
        }
    }

    /**
     * Make defaults creation assertions
     *
     * @param $url        string the creation url
     * @param $formValues array  an array of values which should validate the form
     */
    protected function makeCreateAssertions($url, $formValues)
    {
        $crawler = $this->client->request('GET', $url);
        $res = $this->client->getResponse();
        $this->assertEquals(200, $res->getStatusCode(), $res->getContent());

        $button = $crawler->selectButton('Create');
        $form = $button->form();
        $node = $form->getFormNode();
        $actionUrl = $node->getAttribute('action');
        $uniqId = substr(strchr($actionUrl, '='), 1);

        foreach ($formValues as $key => $value) {
            $form[$uniqId.'[' . $key . ']'] = $value;
        }

        $this->client->submit($form);
        $res = $this->client->getResponse();

        // If we have a 302 redirect, then all is well
        $this->assertEquals(302, $res->getStatusCode(), $res->getContent());
    }

    /**
     * Make defaults deletion assertions
     *
     * @param $url string the deletion url
     */
    protected function makeDeleteAssertions($url)
    {
        $crawler = $this->client->request('GET', $url);
        $res = $this->client->getResponse();
        $this->assertEquals(200, $res->getStatusCode(), $res->getContent());

        $button = $crawler->selectButton('Yes, delete');
        $form = $button->form();

        $this->client->submit($form);
        $res = $this->client->getResponse();

        // If we have a 302 redirect, then all is well
        $this->assertEquals(302, $res->getStatusCode(), $res->getContent());
    }

    /**
     * Make defaults show assertions
     *
     * @param $url              string the edition url
     * @param $containsElements array  an array of elements which should be in the show page
     */
    protected function makeShowAssertions($url, $containsElements)
    {
        $crawler = $this->client->request('GET', $url);
        $res = $this->client->getResponse();
        $this->assertEquals(200, $res->getStatusCode(), $res->getContent());

        foreach ($containsElements as $element) {
            $this->assertCount(1, $crawler->filter('html:contains("' . $element . '")'), $res->getContent());
        }
    }
}
