<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Payum\Core\PayumBuilder;
use Payum\Core\Payum;

class AdminController extends Controller
{
    protected $container;

    public function __construct(/*Container $container*/) {
//        $this->container = $container;
    }

    /**
     * @Route("/admin/projects", name="view_projects")
     */
    public function viewProjectsAction(Request $request)
    {
        $userId = NULL;

        if( $this->container->get('security.authorization_checker')->isGranted( 'IS_AUTHENTICATED_FULLY' ) )
        {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $userId = $user->getId();
        }

/*        $payum = (new PayumBuilder())
        ->addDefaultStorages()

        ->addGateway('paypal_subscription_gateway', [
            'factory' => 'paypal_express_checkout',
            'username'  => 'change it',
            'password'  => 'change it',
            'signature' => 'change it',
            'sandbox'   => true,
        ])

        ->getPayum();        
*/
//        $projects = $this->getDoctrine()->getRepository('AppBundle:Project');
//        $posts = $projects->getAllProjects(1);
        $dql = "SELECT p FROM AppBundle:Project p WHERE p.reviewed = 0";
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery($dql);//->setFirstResult(0)->setMaxResults(100);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            30
        );      
/*        $paginator = new Paginator($query, $fetchJoinCollection = true);
        foreach ($paginator as $project) {
            echo $project->getUser()."\n";
        }
*/
//        dump($paginator);die;
        return $this->render('AppBundle:Admin:view_projects.html.twig', array(
            'pagination' => $pagination
        ));

    }

    /**
     * @Route("/admin/project/review/{projectId}", name="review_project")
     */
    public function reviewProject(Request $request, $projectId) {

        $data = array();
    
        $project = $this->getDoctrine()
            ->getRepository('AppBundle:Project')
            ->find($projectId);

        $form = $this->createFormBuilder($data)
            ->add('android', FileType::class)
            ->getForm();

//            dump($project->getUser()->getId());die;
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $dir = 'uploads/'.$project->getUser()->getId().'/'.$project->getId();
//            $fs = new Filesystem();
//            $fs->mkdir($dir);
            if ($form->isSubmitted() && $form->isValid()) {
                if($form['android']->getData()) {

                        $form['android']->getData()->getClientOriginalExtension();
                        $form['android']->getData()->move($dir.'/bundles/android', $form['android']->getData()->getClientOriginalName());

                        $em = $this->getDoctrine()->getManager();
                        $project->setReviewed(1);
                        $em->persist($project);
                        $em->flush();                        
                }
            }

            // $data is a simply array with your form fields 
            // like "query" and "category" as defined above.
            $data = $form->getData();
        }

        return $this->render('AppBundle:Admin:review_project.html.twig', array(
            'project' => $project,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/bundle/add/{userId}/{projectId}", name="addBundle")
     */
    public function addBundleAction($userId, $projectId)
    {
/*        $message = \Swift_Message::newInstance()
        ->setSubject('Hello Email')
        ->setFrom('alexandru.ivan.daniel@gmail.com')
        ->setTo('alexandru.ivan.daniel@gmail.com')
        ->setBody(
            $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                'AppBundle:Mails:test.html.twig',
                array('name' => '$name')
            ),
            'text/html'
        );
        $this->get('mailer')->send($message);
*/        return $this->render('AppBundle:Admin:add_bundle.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/bundle/edit", name="editBundle")
     */
    public function editBundleAction()
    {
        return $this->render('AppBundle:Admin:edit_bundle.html.twig', array(
            // ...
        ));
    }

}
