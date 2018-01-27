<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/v2/project/{code}", name="project")
     */
    public function indexAction($code)
    {
		$response = new Response();
		$repository = $this->getDoctrine()->getRepository('AppBundle:Project');
		$project = $repository->findOneByCode($code);
		$user = $project->getUser();
        if($user->getDetails()->getType()) {

			$path = $this->get('kernel')->getRootDir().'/../web/uploads/'.$user->getId().'/'.$project->getId().'/sources.zip';
			$path = realpath($path);

			$response->headers->set('Content-type', 'application/octet-stream');
			$response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', 'bundle.zip'));
			$response->setContent(file_get_contents($path));

        } else {
	        throw $this->createNotFoundException('Project not found');
        }

			return $response;

//        return $this->render('ApiBundle:Default:index.html.twig');
    }
}
