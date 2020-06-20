<?php

namespace App\Controller\Back;

use App\Entity\Election;
use App\Entity\User;
use App\Form\ElectionType;
use App\Service\ElectionService;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ElectionController extends AbstractController
{
    private $translator;
    private $userService;
    private $electionService;

    public function __construct(TranslatorInterface $translator, UserService $userService, ElectionService $electionService)
    {
        $this->translator = $translator;
        $this->userService = $userService;
        $this->electionService = $electionService;
    }

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR') or is_granted('ROLE_MEMBER')")
     * @Route("user/elections/pending", name="back.elections.pending", methods={"GET"})
     */
    public function pending(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('back-office/pages/election/list-elections.html.twig', [
            'title' => 'back.default.pages.elections.pending.heading',
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
            'elections' => $this->electionService->getElections(Election::STATUS_PENDING),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR') or is_granted('ROLE_MEMBER')")
     * @Route("user/elections/all", name="back.elections.all", methods={"GET"})
     */
    public function all(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('back-office/pages/election/list-elections.html.twig', [
            'title' => 'back.default.pages.elections.list.heading',
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
            'elections' => $this->electionService->getElections(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR') or is_granted('ROLE_MEMBER')")
     * @Route("user/elections/show/{election}", name="back.elections.show", methods={"GET"})
     * @param Election $election
     * @return Response
     */
    public function show(Election $election): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('back-office/pages/election/show-election.html.twig', [
            'title' => 'back.default.pages.elections.show.heading',
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
            'election' => $election,
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR')")
     * @Route("user/elections/add", name="back.elections.add", methods={"GET", "POST"})
     * @Route("user/elections/edit/{election}", name="back.elections.edit", methods={"GET", "POST"})
     * @param Request $request
     * @param Election $election
     * @return Response
     */
    public function addOrEdit(Request $request, ?Election $election = null): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $isEdition = ($election !== null);

        $election = $election ?? new Election();

        $form = $this->createForm(ElectionType::class, $election);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->electionService->saveElection($election);
            return $this->redirectToRoute('back.elections.all');
        }

        return $this->render('back-office/pages/election/add-edit-election.html.twig', [
            'title' => 'back.default.pages.elections.add.heading',
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
            'form' => $form->createView(),
            'isEdition' => $isEdition,
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR')")
     * @Route("user/elections/publish", name="back.elections.publish", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function publish(Request $request): Response
    {
        return $this->changeElectionStatus($request, Election::STATUS_PENDING);
    }

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR')")
     * @Route("user/elections/end", name="back.elections.end", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function end(Request $request): Response
    {
        return $this->changeElectionStatus($request, Election::STATUS_FINISHED);
    }

    private function changeElectionStatus(Request $request, string $status): Response
    {
        if ($this->isCsrfTokenValid('change_election_status', $request->request->get('csrf_token'))) {
            $election = $this->electionService->getElectionById($request->request->get('election'));
            if ($election) {
                $election->setStatus($status);
                $this->electionService->saveElection($election);
                return $this->redirect($request->headers->get('referer'));
            }
        }
        throw $this->createAccessDeniedException($this->translator->trans('common.access_denied', [], 'translations', $request->getLocale()));
    }
}