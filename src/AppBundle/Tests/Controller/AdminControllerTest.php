<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function testAddbundle()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/addBundle');
    }

    public function testEditbundle()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/editBundle');
    }

}
