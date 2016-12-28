<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PaymentController extends Controller
{
    /**
     * @Route("/done")
     */
    public function doneAction()
    {
        return $this->render('ApiBundle:Payment:done.html.twig', array(
            // ...
        ));
    }

}
