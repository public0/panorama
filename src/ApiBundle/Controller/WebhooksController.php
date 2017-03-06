<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Stripe\Stripe;

class WebhooksController extends Controller
{
    /**
     * @Route("/paymentbounce")
     */
    public function paymentBounceAction()
    {
		Stripe::setApiKey($this->container->getParameter('secret_key'));

		$input = @file_get_contents("php://input");
		$event_json = json_decode($input);

		$response = new Response();
		$response->setStatusCode(Response::HTTP_OK);

		return $response;

/*        return $this->render('ApiBundle:Webhooks:payment_bounce.html.twig', array(
            // ...
        ));
*/    }

}
