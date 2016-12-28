<?php

namespace ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PaymentControllerTest extends WebTestCase
{
    public function testDone()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/done');
    }

}
