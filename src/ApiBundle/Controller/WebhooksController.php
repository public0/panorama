<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class WebhooksController extends Controller
{
    /**
     * @Route("/paymentBounce")
     */
    public function paymentBounceAction()
    {
        return $this->render('ApiBundle:Webhooks:payment_bounce.html.twig', array(
            // ...
        ));
    }

}
