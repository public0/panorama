<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/API/project/{code}", name="project")
     */
    public function indexAction($code)
    {
/*        $projects = $this->getDoctrine()
            ->getRepository('AppBundle:Project')
            ->findBy([$code], ['code' => 'DESC']);
*/
		$repository = $this->getDoctrine()->getRepository('AppBundle:Project');
		$project = $repository->findOneByCode($code);
		$user = $project->getUser();
//		$path = $this->get('kernel')->getRootDir().'/../web/uploads/'.$user->getId().'/'.$project->getId().'/bundles/android/bundle';
		$path = $this->get('kernel')->getRootDir().'/../web/uploads/'.$user->getId().'/'.$project->getId().'/sources.zip';
/*
		echo $path;die;
		chmod($path, 0755);
*/
		$path = realpath($path);
//		$path = realpath($this->get('kernel')->getRootDir().'/../web/uploads/'.$user->getId().'/'.$project->getId().'/bundle/'.$project->getId());
//dump($path);die;
		$response = new Response();
		$response->headers->set('Content-type', 'application/octet-stream');
		$response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', 'bundle.zip'));
		$response->setContent(file_get_contents($path));

		return $response;        

//        return $this->render('ApiBundle:Default:index.html.twig');
    }
}
