<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Entity\Images;
use AppBundle\Entity\Timebase;
use AppBundle\Form\AddProjectType;
use AppBundle\Form\ProjectType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Finder\Finder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/*use Payum\Core\Model\Order;
use Payum\Core\Model\Payment;
use Payum\Core\Model\PaymentToken;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Capture;
use Payum\Core\PayumBuilder\GatewayFactory;
use Payum\Core\Payum;
use Payum\Core\Request\GetHumanStatus;
*/
use Payum\Core\Bridge\Symfony\Builder;
use Payum\Core\PayumBuilder;

use Payum\Stripe\Request\Api\CreateCustomer;
use Stripe\Subscription;
use Payum\Core\Model\PaymentInterface;
use Stripe\Stripe;
use Stripe\Customer;

use Braintree\Configuration;
use Braintree\ClientToken;
use Braintree\Plan as BPlan;
use Braintree\Customer as BCustomer;


//use Payum\Paypal\ExpressCheckout\Nvp\PaypalExpressCheckoutGatewayFactory;

class ProjectsController extends Controller
{
    /**
     * @Route("/projects", name="projects")
     */
    public function indexAction(Request $request)
    {
//        Stripe::setApiKey($this->container->getParameter('secret_key'));
        $user = NULL;        
        $userId = NULL;        
        $showCreateProject = FALSE;
        $customer = NULL;

        if( $this->container->get( 'security.authorization_checker' )->isGranted( 'IS_AUTHENTICATED_REMEMBERED' ) )
        {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $userId = $user->getId();
        } else {
            throw $this->createNotFoundException('The product does not exist');
        }
        $status = $user->getDetails()->getStatus();

        if($status) {
            if($user->getDetails()->getType() == 1) {
                $showCreateProject = TRUE;            
            } else {
                if((int)$user->getDetails()->getPcount() < 1) {
                    $showCreateProject = TRUE;
                } else {
                    $showCreateProject = FALSE;
                }
            }
        } else {
            $showCreateProject = FALSE;
        }

/*        if( $user->getDetails() && $user->getDetails()->getCustomer() && $user->getDetails()->getStatus() ) {
            $customer = Customer::retrieve($user->getDetails()->getCustomer());
            if($customer->subscriptions->data) {
                $showCreateProject = TRUE;
            } elseif($user->getDetails()->getStatus() && (int)$user->getDetails()->getPcount() < 1) {
                $showCreateProject = TRUE;
            } else {
                $showCreateProject = FALSE;            
            }
        } else {
            if($user->getDetails()->getStatus() && (int)$user->getDetails()->getPcount() < 1) {
                $showCreateProject = TRUE;
            } else{
                $showCreateProject = FALSE;            
            }
        }
*/
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
            'images' => $images,
        ]);
    }

    /**
     * @Route("/project/add/", name="add_project")
     */
    public function addAction(Request $request)
    {
//        Stripe::setApiKey($this->container->getParameter('secret_key'));
        $user = NULL;
        $userId = NULL;
        $customer = NULL;
        $type = 0;
        $prepend ='sample_';

        if( $this->container->get( 'security.authorization_checker' )->isGranted( 'IS_AUTHENTICATED_FULLY' ) )
        {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $userId = $user->getId();
        }
        $status = $user->getDetails()->getStatus();


//        dump($sub);die;

/*        if($status) {
            if($user->getDetails()->getType() != 1 && (int)$user->getDetails()->getPcount() > 1) {
                return $this->redirectToRoute('projects', array());            
            }

            // if user had at ony point a stripe customer id
            if($cusStr = $user->getDetails()->getCustomer()) {
                // if user still has a stripe cutomer id
                if($customer = Customer::retrieve($cusStr)) {
                    // if user has any subscriptions
                    if(!$customer->subscriptions->data && (int)$user->getDetails()->getPcount() >= 1) {
                        return $this->redirectToRoute('projects', array());
                    } elseif($customer->subscriptions) {
                        $type = 1;
                        $prepend = '';
                    }
                }
            } elseif((int)$user->getDetails()->getPcount() >= 1) {
                return $this->redirectToRoute('projects', array());
            }
        } else {
            return $this->redirectToRoute('projects', array());            
        }
*/
        $em = $this->getDoctrine()->getManager();

        $project = new Project();
        $form = $this->createForm(AddProjectType::class, $project);
        $form->remove('images');
        $form->remove('exporter');
        $form->handleRequest($request);


        $brain = $this->get('braintree');
        $brain->setToken($user);
        if($customer = $user->getDetails()->getCustomer() && $brain->getActiveSubscription($user->getDetails()->getCustomer())) {
            $type = 1;
            $prepend = '';
        } elseif((int)$user->getDetails()->getPcount() >= 1) {
            return $this->redirectToRoute('projects', array());
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find((int)$userId);

            $file = $project->getFace();
            if($file) {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move(
                    $this->getParameter('face_dir'),
                    $fileName
                );

                $project->setFace($fileName);
            } else {
                $project->setFace('');
            }

            $time = $this->getDoctrine()
            ->getRepository('AppBundle:Timebase')
            ->findOneById(1);

            $timeDiff = time() - $time->getInitstamp();
            $timeBase36 = $time->getStamp36() + $timeDiff;
            $base65Code = base_convert($timeBase36, 10, 36);

            $project->setUser($user);
            $project->setAndroid(1);
            $project->setReviewed(0);
            $project->setType($type);
            $project->setCode($prepend.$base65Code);

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
/*        $email = $this->container->getParameter('mailer_user');
        $password = $this->container->getParameter('mailer_password');

        $transport = new \Swift_SmtpTransport("smtp.office365.com", 587);
        $transport->setUsername($email);
        $transport->setPassword($password);
        $transport->setEncryption('tls');

         
        $mailer = new \Swift_Mailer($transport);

        $message = new \Swift_Message();

        $message->setSubject('Hello Email')
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo('')
            ->setBody(
                'AAAAAAAAAAAAAAAAAAA'
            );

        $mailer->send($message);
*/
        $showCubemap = FALSE;
        $user = NULL;
//        Stripe::setApiKey($this->container->getParameter('secret_key'));

        $userId = NULL;        
        if( $this->container->get( 'security.authorization_checker' )->isGranted( 'IS_AUTHENTICATED_FULLY' ) )
        {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $userId = $user->getId();
        }

        $status = $user->getDetails()->getStatus();
        if($status) {
            if($user->getDetails()->getType() == 1) {
                $brain = $this->get('braintree');
                $clientToken = $brain->setToken($user);
                $plan = $brain->getActivePlan($user->getDetails()->getCustomer());

                $cubemaps = $brain->planData($plan, 'cubemap_count');

                //$customer = Customer::retrieve($user->getDetails()->getCustomer());
                if((int)$user->getDetails()->getActiveCubeCount() < $cubemaps) {
                    $showCubemap = TRUE;
                } else {
                    $showCubemap = FALSE;
                }
            } elseif($user->getDetails()->getType() != 1 && (int)$user->getDetails()->getActiveCubeCount() < 2) {
                $showCubemap = TRUE;
            }
        } else {
            return $this->redirectToRoute('projects');            
        }

/*        if((int)$user->getDetails()->getActiveCubeCount() >= 2) {
            $showCubemap = FALSE;
        } elseif((int)$user->getDetails()->getActiveCubeCount() < 2) {
            $showCubemap = TRUE;
        }

        $status = $user->getDetails()->getStatus();
        if($user->getDetails()) {
            if($user->getDetails()->getCustomer()) {
                if($customer = Customer::retrieve($user->getDetails()->getCustomer())) {
                    if($customer->subscriptions->data) {
                        if((int)$user->getDetails()->getActiveCubeCount() < (int)$customer->subscriptions->data[0]->plan->metadata->cubemap_count) {
                            $showCubemap = TRUE;
                        }                        
                    }
                }
            }
        }
*/
/*        if($user->getDetails() && $user->getDetails()->getCustomer()) {
            $customer = Customer::retrieve($user->getDetails()->getCustomer());
            if($customer->subscriptions->data) {
                if((int)$user->getDetails()->getActiveCubeCount() < (int)$customer->subscriptions->data[0]->plan->metadata->cubemap_count) {
                    $showCubemap = TRUE;
                }
            } else {
                if((int)$user->getDetails()->getActiveCubeCount() >= 2) {
                    $showCubemap = FALSE;            
                }
            }
        } else {
            $showCubemap = FALSE;
        }
*/
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
            ->findOneBy(['id'=>$projectId, 'user'=>$user]);
        if(!$project) {
            return $this->redirectToRoute('projects');                        
        }
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ProjectType::class);
        $form->handleRequest($request);
        $dir = 'uploads/'.$userId.'/'.$projectId;

        $fs = new Filesystem();
        $images = $this->getDoctrine()
                ->getRepository('AppBundle:Images')
                ->findBy(['project' => $projectId, 'status' => 1], ['plan' => 'ASC']);

        $formats = $this->getDoctrine()
                ->getRepository('AppBundle:Exporter')
                ->findAll();

        $imageCount = count($images);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            try {
                if($form['images']->getData()) {
                    $fs->mkdir($dir.'/images');
                    $fs->mkdir($dir.'/backups');
                    $newName = md5($form['images']->getData()->getClientOriginalName().time()).'.'.
                    $form['images']->getData()->getClientOriginalExtension();

                    $image = new Images();
                    $image->setName($newName);
                    $image->setWidth(null !== ($form['width']->getData())?$form['width']->getData():0);
                    $image->setHeight(null !==($form['height']->getData())?$form['height']->getData():0);
                    $image->setExporter($form['exporter']->getData());
                    $image->setProject($project);
                    $image->setCreated(new \DateTime());
                    $image->setStatus(1);
                    $image->setPlan(++$imageCount);
                    $image->setTitle($form['title']->getData());

                    $em->persist($image);

                    $form['images']->getData()->move($dir.'/images', $newName);

                }
                $user = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->find((int)$userId);
                $user->getDetails()->incCubeCount();
                $user->getDetails()->incActiveCubeCount();

/*                $file = $project->getFace();
                if($file) {
                    $fileName = md5(uniqid()).'.'.$file->guessExtension();
                    $file->move(
                        $this->getParameter('face_dir'),
                        $fileName
                    );

                    $project->setFace($fileName);
                }
*/
                $project->setUser($user);
                $project->setReviewed(0);
                $project->setType($user->getDetails()->getType());

                $em->persist($user);
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
        return $this->render('projects/project_edit_2.html.twig', [
            'form' => $form->createView(),
            //'files' => $finder,
            'images' => $images,
            'imageCount' => $imageCount,
            'formats' => $formats,
            'project' => $project,
            'showCubemap' => $showCubemap
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
                ->findBy(['id' => $image_id]);

        $dir = 'uploads/'.$userId;
        $finder = new Finder();
        $finder->files()->in($dir);
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        return $this->render('projects/sample2.html.twig', [
            'baseurl' => $baseurl,
            'images' => $image,
            'userId' => $userId,
            'project' => $project,
            'files' => $finder
        ]);

    }

}
