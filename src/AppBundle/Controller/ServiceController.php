<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Finder;

class ServiceController extends Controller
{
    /**
     * @Route("/show/{code}", name="show_project")
     */
    public function showAction(Request $request, $code)
    {
        $titles = [];
        $mtls = [];

		$repository = $this->getDoctrine()->getRepository('AppBundle:Project');
		$project = $repository->findOneByCode($code);
        $userId = $project->getUser()->getId();

		$images = $this->getDoctrine()->getRepository('AppBundle:Images');
		$images = $images->findBy(['project' => $project->getId(), 'status' => 1], ['plan' => 'ASC']);
        foreach ($images as $image) {
            $dir = 'uploads/'.$userId.'/'.$project->getId().'/images/'.$image->getTitle();
            $title[] = $image->getTitle();
            $dir = 'uploads/'.$userId.'/'.$project->getId().'/images/'.$image->getTitle();
            if(is_dir($dir)) {            
                $finder = new Finder();
                $finder->files()->in($dir)->name('*.txt');
                $iterator = $finder->getIterator();
                $iterator->rewind();
                $mtls[] = $iterator->current()->getFilename();
                $image->mtl = $iterator->current()->getFilename();
            } else {
                $image->mtl = '';
            }
        }

        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

/*        $dir = 'uploads/'.$userId.'/'.$project_id.'/images/'.$image[0]->getTitle();
        $finder = new Finder();
        if(is_dir($dir)) {            
            $finder->files()->in($dir)->name('*.txt');
            $iterator = $finder->getIterator();
            $iterator->rewind();
            $mtlName = $iterator->current()->getFilename();
        }
*/
        return $this->render('AppBundle:Service:show.html.twig', array(
            'baseurl' => $baseurl,
            'userId' => $userId,
            'project' => $project,
            'images' => $images,
            'projectType' => $project->getType(),
            'title' => json_encode($titles),
            'mtls' => $mtls
        ));
    }

}
