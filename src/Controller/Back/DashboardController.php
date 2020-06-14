<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR') or is_granted('ROLE_MEMBER') or is_granted('ROLE_CANDIDATE')")
     * @Route("/dashboard", name="back.dashboard")
     */
    public function home()
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('back-office/pages/dashboard.html.twig', [
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
        ]);
    }
}
