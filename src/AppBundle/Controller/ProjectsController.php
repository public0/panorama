<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Entity\Images;
use AppBundle\Form\AddProjectType;
use AppBundle\Form\ProjectType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Finder\Finder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Payum\Core\Model\Order;
use Payum\Core\Model\Payment;
use Payum\Core\Model\PaymentToken;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Capture;
use Payum\Core\PayumBuilder\GatewayFactory;
use Payum\Core\Payum;
use Payum\Core\Request\GetHumanStatus;

use Payum\Core\Bridge\Symfony\Builder;
use Payum\Core\PayumBuilder;

use Payum\Stripe\Request\Api\CreateCustomer;
use Stripe\Subscription;
use Payum\Core\Model\PaymentInterface;
use Stripe\Stripe;
use Stripe\Customer;

//use Payum\Paypal\ExpressCheckout\Nvp\PaypalExpressCheckoutGatewayFactory;

class ProjectsController extends Controller
{
    /**
     * @Route("/projects", name="projects")
     */
    public function indexAction(Request $request)
    {
        Stripe::setApiKey($this->container->getParameter('secret_key'));
        $user = NULL;        
        $userId = NULL;        
        $showCreateProject = FALSE;
        if( $this->container->get( 'security.authorization_checker' )->isGranted( 'IS_AUTHENTICATED_REMEMBERED' ) )
        {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $userId = $user->getId();
        } else {
            throw $this->createNotFoundException('The product does not exist');
        }
        $customer = Customer::retrieve($user->getDetails()->getCustomer());
        if((int)$user->getDetails()->getPcount() < (int)$customer->subscriptions->data[0]->plan->metadata->project_count) {
            $showCreateProject = TRUE;
        }

        $projects = $this->getDoctrine()
            ->getRepository('AppBundle:Project')
            ->findBy(['user' => $userId, 'status' => [0,1]], ['user' => 'DESC']);
        $images = NULL;
        foreach ($projects as $project) {
            $images[] = $this->getDoctrine()
                    ->getRepository('AppBundle:Images')
                    ->findOneBy(['project' => $project->getId()], ['plan' => 'ASC']);

//            dump($image);
        }            
        //dump($projects[0]);
 //      dump($images[0]->getName());
   //    die;
        return $this->render('projects/projects.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'projects' => $projects,
            'showCreateProject' => $showCreateProject,
            'images' => $images
        ]);
    }

    /**
     * @Route("/project/add/", name="add_project")
     */
    public function addAction(Request $request)
    {
        Stripe::setApiKey($this->container->getParameter('secret_key'));
        $user = NULL;
        $userId = NULL;
        if( $this->container->get( 'security.authorization_checker' )->isGranted( 'IS_AUTHENTICATED_FULLY' ) )
        {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $userId = $user->getId();
        }
        $customer = Customer::retrieve($user->getDetails()->getCustomer());
        if((int)$user->getDetails()->getPcount() >= (int)$customer->subscriptions->data[0]->plan->metadata->project_count) {
            return $this->redirectToRoute('projects', array());            
        }
        $em = $this->getDoctrine()->getManager();

        $project = new Project();
        $form = $this->createForm(AddProjectType::class, $project);
        $form->remove('images');
        $form->remove('exporter');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find((int)$userId);

            $project->setUser($user);
            $project->setAndroid(1);
            $project->setReviewed(0);
            $project->setCode();
            $user->getDetails()->incPcount();
            $em->persist($project);
            $em->persist($user);
            $em->flush();
            $dir = 'uploads/'.$userId.'/'.$project->getId().'/bundles';
            $fs = new Filesystem();
            $fs->mkdir($dir);
//            return $this->redirectToRoute('edit_project', array('projectId' => $project->getId()));

            return $this->redirectToRoute('projects');
        }

        return $this->render('projects/project_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/project/edit/{projectId}", name="edit_project")
     */
    public function editAction(Request $request, $projectId)
    {
/*        $subscription = new \ArrayObject([
          "customer" => 'cus_9vkHwjipijIfxq',
          "plan" => "starter"
        ]);

        $this->get('payum')->getGateway('stripe_checkout')->execute(Subscription::create($subscription));
*/
        $userId = NULL;        
        if( $this->container->get( 'security.authorization_checker' )->isGranted( 'IS_AUTHENTICATED_FULLY' ) )
        {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $userId = $user->getId();
        }

//        PAYMENT CODE
/*        $payum = (new PayumBuilder())
        ->addDefaultStorages()

        ->addGateway('gatewayName', [
            'factory' => 'paypal_express_checkout',
            'username'  => 'change it',
            'password'  => 'change it',
            'signature' => 'change it',
            'sandbox'   => true,
        ])

        ->getPayum();


        $payment = new Payment;
        $payment->setNumber(uniqid());
        $payment->setCurrencyCode('EUR');
        $payment->setTotalAmount(1230); // 1.230 EUR
//        $payment->setDescription('A description');
//        $payment->setClientId('50500');
//        $payment->setClientEmail('foo@example.com');
        $payment->setDetails([
                'RETURNURL' => 'http://example.com',
                'CANCELURL' => 'http://example.com/1/1'
            ]);
        $gateway = $this->get('payum')->getGateway('paypal_express_checkout_and_doctrine_orm');
        $capture = new Capture($payment);
*/
        $project = $this->getDoctrine()
            ->getRepository('AppBundle:Project')
            ->find((int)$projectId);

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        $dir = 'uploads/'.$userId.'/'.$projectId;

        $fs = new Filesystem();
        $images = $this->getDoctrine()
                ->getRepository('AppBundle:Images')
                ->findBy(['project' => $projectId], ['plan' => 'ASC']);

        $formats = $this->getDoctrine()
                ->getRepository('AppBundle:Exporter')
                ->findAll();

        $imageCount = count($images);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            try {
                if($form['images']->getData()) {
                    $fs->mkdir($dir.'/images');
                    $newName = md5($form['images']->getData()->getClientOriginalName().time()).'.'.
                    $form['images']->getData()->getClientOriginalExtension();

                    $image = new Images();
                    $image->setName($newName);
                    $image->setExporter($form['exporter']->getData());
                    $image->setProject($project);
                    $image->setCreated(new \DateTime());
                    $image->setStatus(1);
                    $image->setPlan(++$imageCount);
                    $image->setTitle('');

                    $em->persist($image);

                    $form['images']->getData()->move($dir.'/images', $newName);                    
                }
                $user = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->find((int)$userId);

                $project->setUser($user);
                $project->setReviewed(0);

                $em->persist($project);
                $em->flush();

            } catch (IOExceptionInterface $e) {
                echo "An error occurred while creating your directory at ".$e->getPath();
            }

            if($data->getStatus() === 2) {

/*                $images = $this->getDoctrine()
                    ->getRepository('AppBundle:Images')
                    ->findBy(['project' => $projectId], ['id' => 'ASC']);
                $data = [];
                $i = 0;

                $f = fopen($dir.'/images/data.csv', 'w');
                foreach ($data as $fields) {
                    fputcsv($f, $fields);
                }


                $zip = new \ZipArchive();

                $ret = $zip->open($dir.'/sources.zip', \ZipArchive::CREATE|\ZipArchive::OVERWRITE);
                if ($ret !== TRUE) {
                    printf('Failed with code %d', $ret);
                } else {
                    $options = array('add_path' => 'sources/', 'remove_path' => $dir.'/images');
                    $zip->addPattern('/\.(?:jpg|png|csv|hdr)$/', $dir.'/images', $options);
                    $zip->close();

                    $message = \Swift_Message::newInstance()
                    ->setSubject('Project Published')
                    ->setFrom('alexandru.ivan.daniel@gmail.com')
                    ->setTo('alexandru.ivan.daniel@gmail.com')
                    ->setBody(
                        $this->renderView(
                            // app/Resources/views/Emails/registration.html.twig
                            'AppBundle:Mails:test.html.twig',
                            array('project' => $project)
                        ),
                        'text/html'
                    );

                    $this->get('mailer')->send($message);
                }
*/            }

            return $this->redirectToRoute('edit_project', array('projectId' => $project->getId()));

        }
        return $this->render('projects/project_edit.html.twig', [
            'form' => $form->createView(),
            //'files' => $finder,
            'images' => $images,
            'imageCount' => $imageCount,
            'formats' => $formats,
            'project' => $project
        ]);
    }

    /**
     * @Route("/project/del/", name="del_project")
     * @Method({"POST"})
     */
    public function delAction(Request $request)
    {
        $userId = NULL;        
        $user = NULL;
        if( $this->container->get( 'security.authorization_checker' )->isGranted( 'IS_AUTHENTICATED_FULLY' ) )
        {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $userId = $user->getId();
        }
        $project = $this->getDoctrine()
            ->getRepository('AppBundle:Project')
            ->find((int)$request->request->get('pid'));

        $em = $this->getDoctrine()->getManager();
        // status 2 == deleted
        $project->setStatus(2);
        $user->getDetails()->decPcount();

        $em->persist($user);
        $em->persist($project);
        $em->flush();
        return $this->redirectToRoute('projects');
    }

    /**
     * @Route("/project/preview/{id}/{project_id}/{image_id}", name="preview_project")
     */
    public function previewAction(Request $request, $id, $project_id, $image_id) {
        $userId = NULL;        
        if( $this->container->get( 'security.authorization_checker' )->isGranted( 'IS_AUTHENTICATED_FULLY' ) )
        {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $userId = $user->getId();
        }
        $project = $this->getDoctrine()
            ->getRepository('AppBundle:Project')
            ->find((int)$project_id);

        $image = $this->getDoctrine()
                ->getRepository('AppBundle:Images')
                ->find($image_id);

        $dir = 'uploads/'.$userId;
        $finder = new Finder();
        $finder->files()->in($dir);
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        return $this->render('projects/sample2.html.twig', [
            'baseurl' => $baseurl,
            'image' => $image,
            'userId' => $userId,
            'project' => $project,
            'files' => $finder
        ]);

    }

}
