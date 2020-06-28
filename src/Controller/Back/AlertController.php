<?php

namespace App\Controller\Back;

use App\Entity\Application;
use App\Entity\User;
use App\Service\ApplicationService;
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
    private $applicationService;

    public function __construct(UserService $userService, ApplicationService $applicationService)
    {
        $this->userService = $userService;
        $this->applicationService = $applicationService;
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

        return $this->render('back-office/pages/users.html.twig', [
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
            'users' => $this->userService->getUsersByStatus(false),
        ]);
    }

    /**
     * @Route("admin/alerts/applications", name="back.alerts.applications")
     *
     * @return Response
     */
    public function applications(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('back-office/pages/application/list.html.twig', [
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
            'applications' => $this->applicationService->getApplications(Application::STATUS_PENDING)
        ]);
    }
}
