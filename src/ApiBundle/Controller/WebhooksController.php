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
     * @Route("/bounce")
     */
    public function bounceAction() 
    {
    	return new Response('');
    }

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
		switch($event_json->type) {
		        case 'customer.subscription.deleted':
			        $em = $this->getDoctrine()->getManager();
			        $userDetail = $this->getDoctrine()
		                ->getRepository('AppBundle:UserDetails')
		                ->findOneBy(['customer' => $event_json->data->object->customer]);
		            if($userDetail) {
			    	    $userDetail->setType(0);
			        	$em->persist($userDetail);
			        	$em->flush();		            	
		            }

		        break;
		}

		return $response;
/*
        return $this->render('ApiBundle:Webhooks:payment_bounce.html.twig', array(
            // ...
        ));
*/		
    }

}
