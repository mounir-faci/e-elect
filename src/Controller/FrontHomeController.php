<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FrontHomeController extends AbstractController
{
    /**
     * @Route("/", name="front.home")
     */
    public function home()
    {
        return $this->render('front-office/pages/home.html.twig', [

        ]);
    }
}
