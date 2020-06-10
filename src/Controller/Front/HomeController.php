<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
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
