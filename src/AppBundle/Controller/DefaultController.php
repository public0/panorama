<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Stripe\Plan;
use Stripe\Stripe;
use Payum\Stripe\Request\Api\CreateCustomer;
use AppBundle\Entity\Trial;
use AppBundle\Form\TrialType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
//            $d = $this->get('payum')->getGateway('stripe_checkout');
        $plans = Plan::all(NULL, $this->container->getParameter('secret_key'));

//     dump($plans->data[0]->metadata);die;
//        $user = $this->container->get('security.context')->getToken()->getUser();
//        dump($this->getUser()->getRoles());die;
        // replace this example code with whatever you need
        return $this->render('default/home.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'plans' =>$plans,

        ]);
    }
}
