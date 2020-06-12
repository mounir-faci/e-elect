<?php

namespace App\Controller\Back;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlertController extends AbstractController
{

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR') or is_granted('ROLE_MEMBER') or is_granted('ROLE_CANDIDATE')")
     * @Route("admin/alerts/all", name="back.alerts.all")
     *
     * @return Response
     */
    public function all(): Response
    {
        return $this->render('back-office/pages/dashboard.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR') or is_granted('ROLE_MEMBER') or is_granted('ROLE_CANDIDATE')")
     * @Route("admin/alerts/members", name="back.alerts.members")
     *
     * @return Response
     */
    public function members(): Response
    {
        return $this->render('back-office/pages/dashboard.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR') or is_granted('ROLE_MEMBER') or is_granted('ROLE_CANDIDATE')")
     * @Route("admin/alerts/candidates", name="back.alerts.candidates")
     *
     * @return Response
     */
    public function candidates(): Response
    {
        return $this->render('back-office/pages/dashboard.html.twig', [
            'user' => $this->getUser()
        ]);
    }
}
