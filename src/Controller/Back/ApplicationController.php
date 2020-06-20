<?php

namespace App\Controller\Back;

use App\Entity\Application;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApplicationController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR') or is_granted('ROLE_MEMBER')")
     * @Route("/user/application/{application}", name="back.user.application")
     * @param Application $application
     * @return Response
     */
    public function show(Application $application): Response
    {
        return $this->render('back-office/pages/application.html.twig');
    }

}
