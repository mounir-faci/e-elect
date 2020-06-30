<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Service\StatisticService;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    private $userService;
    private $statisticService;

    public function __construct(UserService $userService, StatisticService $statisticService)
    {
        $this->userService = $userService;
        $this->statisticService = $statisticService;
    }

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR') or is_granted('ROLE_MEMBER')")
     * @Route("/dashboard", name="back.dashboard")
     */
    public function home()
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('back-office/pages/dashboard.html.twig', [
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
            'statistics' => $this->statisticService->getStatistics($user),
        ]);
    }
}
