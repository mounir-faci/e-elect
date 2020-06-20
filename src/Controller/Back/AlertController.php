<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMINISTRATOR')")
 */
class AlertController extends AbstractController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route("admin/alerts/all", name="back.alerts.all")
     *
     * @return Response
     */
    public function all(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('back-office/pages/dashboard.html.twig', [
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
        ]);
    }

    /**
     * @Route("admin/alerts/members", name="back.alerts.members")
     *
     * @return Response
     */
    public function members(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('back-office/pages/dashboard.html.twig', [
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
        ]);
    }

    /**
     * @Route("admin/alerts/candidates", name="back.alerts.candidates")
     *
     * @return Response
     */
    public function candidates(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('back-office/pages/dashboard.html.twig', [
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
        ]);
    }
}
