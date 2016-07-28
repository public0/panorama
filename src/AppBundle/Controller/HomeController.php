<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * @Route("/home", name="homepage2")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need


        return $this->render('default/home.html.twig', [
        ]);
    }
}
