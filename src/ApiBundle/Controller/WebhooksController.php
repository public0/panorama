<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Stripe\Stripe;

use Braintree\Configuration;
use Braintree\ClientToken;
use Braintree\WebhookNotification;
use Braintree\WebhookTesting;

class WebhooksController extends Controller
{
    /**
     * @Route("/bounce")
     */
    public function bounceAction() 
    {
		$brain = $this->get('braintree');
        if( $this->container->get( 'security.authorization_checker' )->isGranted( 'IS_AUTHENTICATED_FULLY' ) )
        {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $userId = $user->getId();
        }
		$clientToken = $brain->setToken($user);
		$plan = $brain->getActivePlan($user->getDetails()->getCustomer());
		$sampleNotification = WebhookTesting::sampleNotification(WebhookNotification::SUBSCRIPTION_CHARGED_UNSUCCESSFULLY, $user->getDetails()->getCustomer());
    	$webhookNotification = WebhookNotification::parse(
	        $sampleNotification['bt_signature'],
		    $sampleNotification['bt_payload']
	    );
	    dump($webhookNotification);
    	die;
		if(
		    isset($_POST["bt_signature"]) &&
		    isset($_POST["bt_payload"])
		) {
		    $webhookNotification = WebhookNotification::parse(
		        $_POST["bt_signature"], $_POST["bt_payload"]
		    );

		    // Example values for webhook notification properties
		    $message = $webhookNotification->kind; // "subscription_went_past_due"
		    $message .= $webhookNotification->timestamp; // "Sun Jan 1 00:00:00 UTC 2012"

		    file_put_contents("/tmp/webhook.log", $message, FILE_APPEND);
		}        
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
