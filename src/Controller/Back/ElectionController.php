<?php

namespace App\Controller\Back;

use App\Entity\Election;
use App\Entity\User;
use App\Service\ElectionService;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ElectionController extends AbstractController
{
    private $userService;
    private $electionService;

    public function __construct(UserService $userService, ElectionService $electionService)
    {
        $this->userService = $userService;
        $this->electionService = $electionService;
    }

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR') or is_granted('ROLE_MEMBER') or is_granted('ROLE_CANDIDATES')")
     * @Route("user/elections/pending", name="back.elections.pending", methods={"GET"})
     */
    public function pending (): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('back-office/pages/election/pending.html.twig', [
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
            'elections' => $this->electionService->getElections(Election::STATUS_PENDING),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR') or is_granted('ROLE_MEMBER') or is_granted('ROLE_CANDIDATES')")
     * @Route("user/elections/all", name="back.elections.all", methods={"GET"})
     */
    public function all (): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // todo get elections
        return $this->render('back-office/pages/election/all.html.twig', [
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
            'elections' => $this->electionService->getElections(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR') or is_granted('ROLE_MEMBER') or is_granted('ROLE_CANDIDATES')")
     * @Route("user/elections/show/{election}", name="back.elections.show", methods={"GET"})
     * @param Election $election
     * @return Response
     */
    public function show (Election $election): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('back-office/pages/election/edit.html.twig', [
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
            'election' => $election,
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR')")
     * @Route("user/elections/add", name="back.elections.add", methods={"GET", "POST"})
     */
    public function add (): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // todo add election


        return $this->render('back-office/pages/election/add.html.twig', [
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR')")
     * @Route("user/elections/edit/{election}", name="back.elections.edit", methods={"GET", "POST"})
     * @param Election $election
     * @return Response
     */
    public function edit (Election $election): Response
    {
        /** @var User $user */
        $user = $this->getUser();


        // todo update the posted election

        return $this->render('back-office/pages/election/edit.html.twig', [
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR')")
     * @Route("user/elections/delete", name="back.elections.delete", methods={"POST"})
     */
    public function delete (): Response
    {
        // todo delete the posted election

        return $this->redirectToRoute('back.elections.all');
    }
}