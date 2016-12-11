<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AjaxControllerTest extends WebTestCase
{
    public function testImagesave()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/imageSave');
    }

}
