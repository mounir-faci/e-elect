<?php

namespace App\Controller\Back;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR') or is_granted('ROLE_MEMBER') or is_granted('ROLE_CANDIDATE')")
     * @Route("/dashboard", name="back.dashboard")
     */
    public function home()
    {
        return $this->render('back-office/pages/dashboard.html.twig', [
            'user' => $this->getUser()
        ]);
    }
}
