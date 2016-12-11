<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AjaxController extends Controller
{
    /**
     * @Route("/imageSave")
     */
    public function imageSaveAction(Request $request)
    {
        $userId = NULL;        
        if( $this->container->get( 'security.authorization_checker' )->isGranted( 'IS_AUTHENTICATED_FULLY' ) )
        {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $userId = $user->getId();
        }

		if ($request->getMethod() == 'POST') {
	        $em = $this->getDoctrine()->getManager();

	        $project = $this->getDoctrine()
	            ->getRepository('AppBundle:Project')
	            ->findOneBy(['user' => $userId, 'id' => $_POST['project_id']]);


	        if(!$project) {
	        	return new Response('Problem finding project');
	        }
	        // Save to DB
	        for ($i=0; $i < count($_POST['image_id']); $i++) {
		        $image = $this->getDoctrine()
		            ->getRepository('AppBundle:Images')
		            ->find($_POST['image_id'][$i]);
		        if($_POST['delete'][$i]) {
		        	$image->remove();
		            $em->persist($image);
		            continue;
		        }

		        $image->setTitle($_POST['title'][$i]);
		        $exporter = $this->getDoctrine()
		            ->getRepository('AppBundle:Exporter')
		            ->find((int)$_POST['exporter'][$i]);
		        $image->setExporter($exporter);
		        $image->setPlan($_POST['plan'][$i]);
	            $em->persist($image);
	        }
	        $em->flush();
            $zip = new \ZipArchive();
	        // Save to DB

            // Write to csv
	        $dir = 'uploads/'.$userId.'/'.$project->getId();

	        $images = $this->getDoctrine()
	            ->getRepository('AppBundle:Images')
	            ->findBy(['project' => $_POST['project_id']], ['plan' => 'ASC']);

            $f = fopen($dir.'/images/data.csv', 'w');
/*            $data = [];
                fputcsv($f, [
                	'',
                	'',
                	'',
                	'',
                	]
                );
*/            foreach ($images as $image) {
                fputcsv($f, [
	                	$image->getPlan(),
	                	$image->getExporter()->getId(),
	                	$image->getName(),
	                	$image->getTitle()
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
                $zip->addPattern('/\.(?:jpg|png|csv|hdr)$/', $dir.'/images', $options);
                $zip->close();

			}

			// Archive Project
		}
//		var_dump($project);
		return new Response('');
/*        return $this->render('AppBundle:Ajax:image_save.html.twig', array(
        ));
*/    }

}
