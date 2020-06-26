<?php


namespace App\Controller\Back;


use App\Entity\User;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VoteController extends AbstractController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR') or is_granted('ROLE_MEMBER')")
     * @Route("/user/votes/history", name="back.user.votes.history", methods={"GET"})
     * @return Response
     */
    public function history(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('back-office/pages/votes-list.html.twig', [
            'title' => 'back.default.pages.votes.heading',
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
            'votes' => $user->getVotes(),
        ]);

    }
}