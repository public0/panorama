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
use AppBundle\Form\ContactType;

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
            // Refill the fields in case the form is not valid.
            $form->handleRequest($request);

            if($form->isValid()){
                // Send mail

                if($this->sendEmail($form->getData())){

                    // Everything OK, redirect to wherever you want ! :
                   return $this->redirectToRoute('contact', ['success' => true]);
                }else{
                    // An error ocurred, handle
                   return $this->redirectToRoute('contact', ['success' => false]);
                }
            }
        }

        return $this->render('default/contact.html.twig', array(
            'form' => $form->createView()
        ));

        return $this->render('default/contact.html.twig', [
        ]);
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
            ->setTo($data['name'])
            ->setBody(
                $data['message']
            );

        $mailer->send($message);

    }

}
