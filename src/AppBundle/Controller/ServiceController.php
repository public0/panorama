<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceController extends Controller
{
    /**
     * @Route("/show/{code}", name="show_project")
     */
    public function showAction(Request $request, $code)
    {
		$repository = $this->getDoctrine()->getRepository('AppBundle:Project');
		$project = $repository->findOneByCode($code);

		$images = $this->getDoctrine()->getRepository('AppBundle:Images');
		$images = $images->findBy(['project' => $project->getId(), 'status' => 1], ['plan' => 'ASC']);
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        return $this->render('AppBundle:Service:show.html.twig', array(
            'baseurl' => $baseurl,
            'userId' => $project->getUser()->getId(),
            'project' => $project,
            'images' => $images,
            'projectType' => $project->getType()
        ));
    }

}
