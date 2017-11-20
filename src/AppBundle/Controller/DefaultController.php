<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Stripe\Plan;
use Stripe\Stripe;
use Payum\Stripe\Request\Api\CreateCustomer;
use AppBundle\Entity\Trial;
use AppBundle\Form\TrialType;
use AppBundle\Form\ContactType;
use Symfony\Component\HttpFoundation\Session\Session;
use Braintree\Plan as BPlan;

class DefaultController extends Controller
{
    /**
     * @Route("googlea4a2a626b1e56f21.html", name="gtools")
     */
    public function googleToolsAction(Request $request)
    {
        return new Response('google-site-verification: googlea4a2a626b1e56f21.html');
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
//            $d = $this->get('payum')->getGateway('stripe_checkout');
//        $plans = Plan::all(NULL, $this->container->getParameter('secret_key'));
        $brain = $this->get('braintree');
        $clientToken = $brain->setToken();

        $plans = BPlan::all();
//        var_dump($plans[0]->discounts);die;
//     dump($plans->data[0]->metadata);die;
//        $user = $this->container->get('security.context')->getToken()->getUser();
//        dump($this->getUser()->getRoles());die;
        // replace this example code with whatever you need
        return $this->render('default/home.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'plans' =>$plans,

        ]);
    }

    /**
     * @Route("/terms", name="terms")
     */
    public function termsAction(Request $request)
    {
        return $this->render('default/terms.html.twig', [
        ]);
    }


    /**
     * @Route("/about", name="about")
     */
    public function aboutAction(Request $request)
    {
        return $this->render('default/about.html.twig', [
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contactAction(Request $request)
    {
        $form = $this->createForm(ContactType::class, array(
            // To set the action use $this->generateUrl('route_identifier')
            'action' => $this->generateUrl('contact'),
            'method' => 'POST'
        ));

        if ($request->isMethod('POST')) {
            $this->get('session')->getFlashBag()->add('contact', 'Thanks for contacting us, we will reply to you as soon as possible.' );
            // Refill the fields in case the form is not valid.
            $form->handleRequest($request);

            if($form->isValid()){
                // Send mail

                if($this->sendEmail($form->getData())){

                    // Everything OK, redirect to wherever you want ! :
                   return $this->redirectToRoute('contact');
                }else{
                    // An error ocurred, handle
                   return $this->redirectToRoute('contact');
                }
            }
        }

        return $this->render('default/contact.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/privacy-policy", name="privacy")
     */
    public function privacyAction(Request $request)
    {
        return $this->render('default/privacy.html.twig', [
        ]);
    }

    private function sendEmail($data){

        $email = $this->container->getParameter('mailer_user');
        $password = $this->container->getParameter('mailer_password');

        $transport = new \Swift_SmtpTransport("smtp.office365.com", 587);
        $transport->setUsername($email);
        $transport->setPassword($password);
        $transport->setEncryption('tls');
        
        $mailer = new \Swift_Mailer($transport);

        $message = new \Swift_Message();

        $message->setSubject($data['subject'])
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($this->container->getParameter('mailer_user'))
            ->setBody(
                'Message from '.$data['email'].'<br>'.
                $data['message'],
                'text/html'
            );

        $mailer->send($message);

    }

}
