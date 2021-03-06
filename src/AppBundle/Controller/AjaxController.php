<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Filesystem\Filesystem;
use Stripe\Stripe;
use Stripe\Customer;
use AppBundle\Entity\TextToSpeech;
use AppBundle\Entity\Settings;

use Braintree\Configuration;
use Braintree\Customer as BCustomer;
use Braintree\ClientToken;
use Braintree\Subscription;

	
class AjaxController extends Controller
{
    /**
     * @Route("positions")
     */

    public function positionsAction(Request $request)
    {
		$serializer = $this->get('jms_serializer');
    	$image = $request->query->get('image');
        $positions = $this->getDoctrine()
            ->getRepository('AppBundle:TextToSpeech')
            ->findBy(['image'=>$image]);
		$response = $serializer->serialize($positions,'json');
		return new Response($response);

    }

    /**
     * @Route("position")
     */

    public function positionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
    	$sphere  = json_decode($request->request->get('sphere'));
    	$sphere->x = round($sphere->x, 2);
    	$sphere->y = round($sphere->y, 2);
    	$sphere->z = round($sphere->z, 2);
    	$event   = $request->request->get('event');
    	$project = $request->request->get('project');
    	$text = $request->request->get('text');
    	$image = $request->request->get('image');

        $project = $this->getDoctrine()
            ->getRepository('AppBundle:Project')
            ->findOneBy(['id'=>$project]);

        $image = $this->getDoctrine()
            ->getRepository('AppBundle:Images')
            ->findOneBy(['id'=>$image]);

    	$tts = new TextToSpeech();
    	$tts->setProject($project);
    	$tts->setVector($sphere);
    	$tts->setText($text);
    	$tts->setEvent($event);
    	$tts->setImage($image);
        $em->persist($tts);
        $em->flush();

		return new Response();
    }

    /**
     * @Route("/subscribe")
     */
    public function subscribeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $customer = NULL;
        $result = NULL;
        $token = NULL;
        $success = FALSE;
        $brain = $this->get('braintree');
        $clientToken = $brain->setToken($user);

    	$nonce = $request->request->get('data');
    	$plan = $request->request->get('plan');

	   	$customer = $brain->subscribe($plan, $user, $nonce);
			if(!is_array($customer)) {
		  		$userDetails = $user->getDetails();
		  		$userDetails->setCustomer($customer);
		        $userDetails->setType(1);
		        $em->persist($userDetails);
				$em->flush();
				return new Response(1);
			} else {
				return new Response($customer[0]);
			}

	}

	private function toDelete() {
    	die;

  		if($user->getDetails()->getCustomer()) {

			if($result = BCustomer::find($user->getDetails()->getCustomer())) {
				$success = TRUE;
				$token = $result->paymentMethods[0]->token;
			} else {
		    	$result = BCustomer::create([
		    		'lastName' => $user->getUsername(),
		/*		    'firstName' => '--',
				    'lastName' => '--',
				    'company' => '--',
		*/		    'email' => $user->getEmail(),
		/*		    'phone' => '281.330.8004',
				    'fax' => '419.555.1235',
				    'website' => 'http://example.com',
		*/		    'paymentMethodNonce' => $nonce['nonce']
				]);

		  		$userDetails = $user->getDetails();
		  		$userDetails->setCustomer($result->customer->id);
	            $em->persist($userDetails);
				$success = $result->success;
			}

  		} else {

	    	$result = BCustomer::create([
	    		'lastName' => $user->getUsername(),
	/*		    'firstName' => '--',
			    'lastName' => '--',
			    'company' => '--',
	*/		    'email' => $user->getEmail(),
	/*		    'phone' => '281.330.8004',
			    'fax' => '419.555.1235',
			    'website' => 'http://example.com',
	*/		    'paymentMethodNonce' => $nonce['nonce']
			]);

			$success = $result->success;

	  		$userDetails = $user->getDetails();
	  		$userDetails->setCustomer($result->customer->id);
            $em->persist($userDetails);
  		}

		if ($success) {
			$token = $result->customer->paymentMethods[0]->token;

		    echo($result->customer->id).'|';
		    echo($result->customer->paymentMethods[0]->token);
		} else {
		    foreach($result->errors->deepAll() AS $error) {
		        echo($error->code . ": " . $error->message . "\n");
		    }
		}		

		$result = Subscription::create([
		  'paymentMethodToken' => $token,
		  'planId' => $plan
		]);		


		$em->flush();
		die;

	}	

    /**
     * @Route("/imageCancel")
     */
    public function imageCancelAction(Request $request)
    {
    	$id = $request->request->get('img');
        $em = $this->getDoctrine()->getManager();
	    $image = $this->getDoctrine()
	        ->getRepository('AppBundle:Images')
	        ->findOneBy(['id' => $id, 'status' => 1]);

	    $image->setStatus(0);
        $em->persist($image);
        $em->flush();


		return new Response(0);
    }
    /**
     * @Route("/settings")
     * @Method({"GET"})
     */
    public function getSettingsAction(Request $request)
	{
		$data = $request->query->get('data');
        $image = $this->getDoctrine()
            ->getRepository('AppBundle:Images')
            ->findOneBy(['id'=>$data]);

        $settings = !is_null($image->getSettings())?$image->getSettings()->getSettings():0;

    	return new Response($settings);
	}
    /**
     * @Route("/settings")
     * @Method({"POST"})
     */
    public function settingsAction(Request $request)
    {
    	$data = $request->request->get('data');
    	//return new Response(json_encode($data));
        $em = $this->getDoctrine()->getManager();

        $project = $this->getDoctrine()
            ->getRepository('AppBundle:Project')
            ->findOneBy(['id'=>$data[0]['value']]);

        $image = $this->getDoctrine()
            ->getRepository('AppBundle:Images')
            ->findOneBy(['id'=>$data[1]['value']]);

        $settings = $image->getSettings();

        if (!$settings) {
        	$settings = new Settings();
        }
    	$settings->setProject($project);
    	$settings->setImage($image);
    	$settings->setSettings(json_encode($data));
        $em->persist($settings);

        $image->setSettings($settings);
        $em->persist($image);

        $em->flush();

        $settings = $image->getSettings();

    	return new Response($settings->getSettings());
    }

    /**
     * @Route("/imageSave")
     */
    public function imageSaveAction(Request $request)
    {
//        Stripe::setApiKey($this->container->getParameter('secret_key'));
        $userId = NULL;        
        $fs = new Filesystem();
        $activeCubemaps   = 0;
        $inactiveCubemaps   = 0;
        $totalCubemaps 	  = 0;
        $projectActiveCubemaps = 0;
        $customer = NULL;

        if( $this->container->get( 'security.authorization_checker' )->isGranted( 'IS_AUTHENTICATED_FULLY' ) )
        {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $userId = $user->getId();
        }

		if ($request->getMethod() == 'POST') {
	        $em = $this->getDoctrine()->getManager();
	        sleep(2);
	        if($_POST['image_id']) {
        		$totalCubemaps = count($_POST['image_id']);
	        }

	        $project = $this->getDoctrine()
	            ->getRepository('AppBundle:Project')
	            ->findOneBy(['user' => $userId, 'id' => $_POST['project_id']]);


	        if(!$project) {
	        	return new Response('Problem finding project');
	        }
	        $setImages = $this->getDoctrine()
	            ->getRepository('AppBundle:Images')
	            ->findBy(['project' => $_POST['project_id'], 'status' => 1]);

/*            if($user->getDetails()->getType() == 1 && (int)$user->getDetails()->getActiveCubeCount() < (int)$customer->subscriptions->data[0]->plan->metadata->cubemap_count) {
	            $customer = Customer::retrieve($user->getDetails()->getCustomer());
                $showCubemap = TRUE;
            } elseif($user->getDetails()->getType() != 1 && (int)$user->getDetails()->getActiveCubeCount() < 2) {
                $showCubemap = TRUE;
            }
*/
	        $dir = 'uploads/'.$userId.'/'.$project->getId();

	        // Save to DB
	        for ($i=0; $i < count($_POST['image_id']); $i++) {
		        $image = $this->getDoctrine()
		            ->getRepository('AppBundle:Images')
		            ->find($_POST['image_id'][$i]);

		        if($_POST['delete'][$i]) {
		        	$inactiveCubemaps++;
		        	$image->setStatus(0);
		        	if($fs->exists($dir.'/images/'.$image->getName())) {
			        	$fs->copy($dir.'/images/'.$image->getName(), $dir.'/backups/'.$image->getName());
		        	}
		            $em->persist($image);
		        } else {
		        	$activeCubemaps++;
		        	$image->setStatus(1);
		        	if($fs->exists($dir.'/backups/'.$image->getName())) {
			        	$fs->copy($dir.'/backups/'.$image->getName(), $dir.'/images/'.$image->getName());
			        	$fs->remove($dir.'/backups/'.$image->getName());
			        }
		            $em->persist($image);
		        }

		        $image->setTitle($_POST['title'][$i]);
		        $exporter = $this->getDoctrine()
		            ->getRepository('AppBundle:Exporter')
		            ->find((int)$_POST['exporter'][$i]);
		        $image->setExporter($exporter);
		        $image->setPlan($_POST['plan'][$i]);
	            $em->persist($image);
	        }

	        if($user->getDetails()->getType() == 1) {
//	            $customer = Customer::retrieve($user->getDetails()->getCustomer());

                $brain = $this->get('braintree');
                $clientToken = $brain->setToken($user);
                $plan = $brain->getActivePlan($user->getDetails()->getCustomer());

                $cubemaps = $brain->planData($plan, 'cubemap_count');

	            if(((int)$user->getDetails()->getActiveCubeCount() - (int)count($setImages) + (int)$activeCubemaps) > $cubemaps) {
	            	$response = new Response();
	            	$response->setContent('Active cubemap count exceeded');
	            	return $response;
	            }
	        } else {
	            if(((int)$user->getDetails()->getActiveCubeCount() - (int)count($setImages) + (int)$activeCubemaps) > 2) {
	            	$response = new Response();
	            	$response->setContent('Active cubemap count exceeded');
	            	return $response;
	            }
	        }

            $zip = new \ZipArchive();
	        // Save to DB

			$projectActiveCubemaps = ($user->getDetails()->getCubeCount() - $totalCubemaps) + $activeCubemaps;

//			$projectActiveCubemaps = $user->getDetails()->getCubeCount() - $inactiveCubemaps + $activeCubemaps;
			$user->getDetails()->setActiveCubeCount($projectActiveCubemaps);
			$em->persist($user);
/*            $customer = Customer::retrieve($user->getDetails()->getCustomer());
	        if($projectActiveCubemaps > (int)$customer->subscriptions->data[0]->plan->metadata->cubemap_count) {
				return new Response(1);
	        }
*/
	        $em->flush();

            // Write to csv

	        $images = $this->getDoctrine()
	            ->getRepository('AppBundle:Images')
	            ->findBy(['project' => $_POST['project_id'], 'status' => 1], ['plan' => 'ASC']);

            $f = fopen($dir.'/images/data.csv', 'w');
            foreach ($images as $image) {

		        $imageTexts = $this->getDoctrine()
		            ->getRepository('AppBundle:TextToSpeech')
		            ->findBy(['image' => $image]);
		        $imgFileName = mb_substr($image->getName(), 0, -4).'.csv';
	            $imgFile = fopen($dir.'/images/'.$imgFileName, 'w');

		        $settings = $image->getSettings();
	            $formattedSettings = json_decode($settings->getSettings());
	            $csvSettings = null;
	            foreach ($formattedSettings as $key => $value) {
	            	$csvSettings[$value->name] = $value->value;
	            }

                fputcsv($imgFile, [
	                	$settings->getId(),
	                	'settings',
	                	'settings',
	                	json_encode($csvSettings),
                	]
                );

	            foreach ($imageTexts as $imageText) {

	                fputcsv($imgFile, [
		                	$imageText->getImage()->getId(),
		                	strtolower($imageText->getEvent()),
		                	strtolower($imageText->getText()),
		                	$imageText->getVector(),
	                	]
	                );

				}
				fclose($imgFile);

                fputcsv($f, [
	                	$image->getPlan(),
	                	$image->getExporter()->getId(),
	                	$image->getName(),
	                	$image->getTitle(),
	                	$image->getWidth(),
	                	$image->getHeight(),
	                	$imgFileName
                	]
                );
/*				$stat = fstat($f);
				ftruncate($f, $stat['size']-1);
*/
            }

			fclose($f);
            // Write to csv
			// Archive Project
            $zip = new \ZipArchive();

            $ret = $zip->open($dir.'/sources.zip', \ZipArchive::CREATE|\ZipArchive::OVERWRITE);
            if ($ret !== TRUE) {
                printf('Failed with code %d', $ret);
            } else {
               $options = array('add_path' => 'sources/', 'remove_path' => $dir.'/images');
				foreach ($images as $image) {
					$zip->addFile($dir.'/images/'.$image->getName(), 'sources/'.$image->getName());
					$imageTexts = $dir.'/images/'.mb_substr($image->getName(), 0, -4).'.csv';
	            	$zip->addFile($imageTexts, 'sources/'.mb_substr($image->getName(), 0, -4).'.csv');

					$mDir = $dir.'/images/'.$image->getTitle();
					if(is_dir($mDir)) {
						$zip->addEmptyDir('sources/'.$image->getTitle());
						$files = new \RecursiveIteratorIterator(
						    new \RecursiveDirectoryIterator($mDir),
						    \RecursiveIteratorIterator::LEAVES_ONLY|\RecursiveDirectoryIterator::SKIP_DOTS
						);

						foreach($files as $file) {
//							echo $file->getRealPath().'--'.$file->getFilename().'<br>';
							if(is_file($file->getRealPath()))
								$zip->addFile($file->getRealPath(), 'sources/'.$image->getTitle().'/'.$file->getFilename());
						}
					}

				}
				$zip->addFile($dir.'/images/data.csv', 'sources/data.csv');

/*				$options = array('add_path' => 'sources/', 'remove_path' => $dir.'/images');
				$zip->addPattern('/\.(?:jpg|png|csv|hdr)$/', $dir.'/images', $options);
				$zip->close();
*/
			}


			// Archive Project
		}
		return new Response(0);
/*        return $this->render('AppBundle:Ajax:image_save.html.twig', array(
        ));
*/    }

}
