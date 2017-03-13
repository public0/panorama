<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;
use AppBundle\Entity\UserDetails;
use AppBundle\Form\UserType;
use Payum\Stripe\Request\Api\CreatePlan;
use Payum\Stripe\Request\Api\CreateCustomer;
use Stripe\Subscription;
use Stripe\Plan;
use Stripe\Stripe;
use Stripe\Customer;

class ProfileController extends Controller
{
	// NOT USED
    /**
     * @Route("profile/editz", name="profile_edit")
     */
    public function editAction(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

		$form = $this->createForm(UserType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
		    // data is an array with "name", "email", and "message" keys
		    $data = $form->getData();
			$em = $this->getDoctrine()->getManager();
        	$em->persist($data);
        	$em->flush();
		}
        return $this->render('AppBundle:Profile:edit.html.twig', array(
        	'form' => $form->createView()
            // ...
        ));
    }

    /**
     * @Route("profile/plans", name="profile_plans")
     */
    public function plansAction(Request $request)
	{
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
		$currentPlan = NULL;

		Stripe::setApiKey($this->container->getParameter('secret_key'));
		if($details = $user->getDetails()) {
			// if user has a stripe customer in db
			if($cusStr = $details->getCustomer()) {
				// if user has a stripe customer in stripe
				if($customer = Customer::retrieve($cusStr)) {
					if($customer->subscriptions && $customer->subscriptions->data) {
						$currentPlan = $customer->subscriptions->data[0]->plan->id;
					}
				}
			}
		}

		$plans = Plan::all(NULL, $this->container->getParameter('secret_key'));
		return $this->render('AppBundle:Profile:plans.html.twig', array(
			'currentPlan' => $currentPlan,
			'plans' => $plans
		));
	}

    /**
     * @Route("profile/unsubscribe/", name="profile_unsubscribe")
     */
    public function unsubscribeAction(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

		Stripe::setApiKey($this->container->getParameter('secret_key'));
		if($details = $user->getDetails()) {
			$cusotmer = $details->getCustomer();
//			$sub = \Stripe\Subscription::retrieve("sub_ADb6nr2yloPXrx");
			if($customer = Customer::retrieve($cusotmer)) {
				if($data = $customer->subscriptions->data) {
			        $em = $this->getDoctrine()->getManager();
					$data[0]->cancel();
			        $em = $this->getDoctrine()->getManager();
			        $user->getDetails()->setType(0);
			        $em->persist($user);
					$em->flush();

/*					$user->getDetails()->remove();					
		            $em->persist($user);
		            $em->flush();
*/
		            return $this->redirectToRoute('profile_plans', array());					
				}
			}
//			$sub->cancel();
		}
        return $this->redirectToRoute('profile_plans', array());					

    }

    /**
     * @Route("profile/subscribe/{planId}", name="profile_subscribe")
     */
    public function subscribeAction(Request $request, $planId)
	{
		Stripe::setApiKey($this->container->getParameter('secret_key'));
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $details = NULL;

		$start = new \DateTime();
		$end = new \DateTime();
		$end = $end->modify('+20 year');

		if($request->getMethod() == 'POST') {
	        $em = $this->getDoctrine()->getManager();

			$name   = $request->request->get('card-holder-name');
			$number = $request->request->get('card-number');
			$expiryMonth = $request->request->get('expiry-month');
			$expiryYear  = $request->request->get('expiry-year');
			$cvv  = $request->request->get('cvv');
			$token  = $request->request->get('stripe-token');
			// if user ever had a subscribtion
			if($details = $user->getDetails()) {
				// if user has a stripe customer in db
				if($cusStr = $details->getCustomer()) {
					// if user has a stripe customer in stripe
					if($customer = Customer::retrieve($cusStr)) {
						// if user has active subscription update it
						if($data = $customer->subscriptions->data) {
							$subscription = Subscription::retrieve($data[0]->id);
							$subscription->plan = $planId;
							$subscription->save();
						// if user doesn't have active subscription, make one
						} else {
							$subscription = Subscription::create([
								'customer' => $customer,
								'plan' => $planId
							]);
						}
						// if user doesn't have active customer in stripe, maybe deleted from stripe directly
						// create new customer now and subscribe to plan
					} else {
						$cus = [
				            "email" => $user->getEmail(),
				            "source" => $token
						];
						$customer = Customer::create($cus);

						$subscription = Subscription::create([
							'customer' => $customer,
							'plan' => $planId
						]);
						$userDetails = new UserDetails();
						$userDetails->setPcount(0);
		                $userDetails->setType(1);
						$userDetails->setCustomer($customer->id);
						$user->setDetails($userDetails);
	                    $em->persist($userDetails);
	                    $em->persist($user);
					}
				// no stripe customer account in db update customer for existing entry -- manually deleted from db ?
				} else {
					$cus = [
			            "email" => $user->getEmail(),
			            "source" => $token
					];
					$customer = Customer::create($cus);

					$subscription = Subscription::create([
						'customer' => $customer,
						'plan' => $planId
					]);

					$userDetails = $user->getDetails();
					$userDetails->setCustomer($customer->id);
	                $userDetails->setType(1);
                    $em->persist($user);
				}
			// First time subscriber
			} else {
				$cus = [
		            "email" => $user->getEmail(),
		            "source" => $token
				];
				$customer = Customer::create($cus);

				$subscription = Subscription::create([
					'customer' => $customer,
					'plan' => $planId
				]);
				$userDetails = new UserDetails();
				$userDetails->setPcount(0);
                $userDetails->setType(1);
				$userDetails->setCustomer($customer->id);
				$user->setDetails($userDetails);
	            $em->persist($userDetails);
	            $em->persist($user);

			}
			$em->flush();
            return $this->redirectToRoute('projects');			
		}
		return $this->render('AppBundle:Profile:subscribe.html.twig', array(
			'currentYear' => $start->format('Y'),
			'lastYear' => $end->format('Y')
		));

	}    

}
