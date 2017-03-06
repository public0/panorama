<?php

namespace ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WebhooksControllerTest extends WebTestCase
{
    public function testPaymentbounce()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/paymentBounce');
    }

}
