<?php

namespace App\Controller\Back;

use App\Entity\Application;
use App\Entity\User;
use App\Entity\Vote;
use App\Form\ApplicationType;
use App\Service\ApplicationService;
use App\Service\ElectionService;
use App\Service\UserService;
use App\Service\VoteService;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApplicationController extends AbstractController
{
    private $userService;
    private $electionService;
    private $applicationService;
    private $voteService;
    private $translator;

    public function __construct(UserService $userService, ElectionService $electionService, ApplicationService $applicationService, VoteService $voteService, TranslatorInterface $translator)
    {
        $this->userService = $userService;
        $this->electionService = $electionService;
        $this->applicationService = $applicationService;
        $this->voteService = $voteService;
        $this->translator = $translator;
    }

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR') or is_granted('ROLE_MEMBER')")
     * @Route("/user/applications", name="back.user.application.list")
     * @return Response
     */
    public function applicationsList(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('back-office/pages/application/list.html.twig', [
            'title' => 'back.default.pages.applications.heading',
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
            'applications' => $user->getApplications(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR') or is_granted('ROLE_MEMBER')")
     * @Route("/user/application/send", name="back.user.application.send")
     * @param Request $request
     * @return Response
     */
    public function send(Request $request): Response
    {
        if (
            $this->isCsrfTokenValid('user_application', $request->request->get('token')) ||
            $this->isCsrfTokenValid('user_application', $request->request->get('application')['token'])
        ) {
            /** @var User $user */
            $user = $this->getUser();
            $electionId = $request->request->get('election') ?? $request->request->get('application')['election'] ?? null;
            $election = $this->electionService->getElectionById(intval($electionId));
            if (!$election) {
                goto END;
            }

            $application = new Application();
            $application
                ->setCandidate($user)
                ->setElection($election);
            $form = $this->createForm(ApplicationType::class, $application);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->applicationService->saveApplication($application);
                return $this->redirectToRoute('back.elections.show', ['election' => $electionId]);
            }

            return $this->render('back-office/pages/application/send.html.twig', [
                'title' => 'back.default.pages.application.heading',
                'user' => $user,
                'notification' => $this->userService->getUserNotification($user),
                'form' => $form->createView(),
            ]);
        }

        END:
        throw $this->createAccessDeniedException($this->translator->trans('common.access_denied', [], 'translations', $request->getLocale()));
    }


    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR') or is_granted('ROLE_MEMBER')")
     * @Route("/user/application/{application}", name="back.user.application.show", methods={"GET", "POST"})
     * @param Request $request
     * @param Application $application
     * @return Response
     */
    public function show(Request $request, Application $application): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($request->getMethod() === Request::METHOD_POST && $this->isCsrfTokenValid('user_vote', $request->request->get('token'))) {
            $vote = new Vote();
            $vote
                ->setVoter($user)
                ->setApplication($application)
                ->setVoteDate(new DateTime());

            $this->voteService->saveVote($vote);
            return $this->redirectToRoute('back.user.votes.history');
        }

        $currentUserAlreadyVoted = $application->getVotes()->filter(
            function (Vote $vote) use ($user) {
                return ($vote->getVoter()->getId() === $user->getId());
            }
        );

        return $this->render('back-office/pages/application/show.html.twig', [
            'title' => 'back.default.pages.vote.heading',
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
            'application' => $application,
            'currentUserIsTheCandidate' => ($application->getCandidate()->getId() === $user->getId()),
            'currentUserAlreadyVoted' => (count($currentUserAlreadyVoted) > 0),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR') or is_granted('ROLE_MEMBER')")
     * @Route("/user/applications/edit/{application}", name="back.user.application.edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Application $application
     * @return Response
     */
    public function edit(Request $request, Application $application): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->applicationService->saveApplication($application);
        }

        return $this->render('back-office/pages/application/edit.html.twig', [
            'title' => 'back.default.pages.application.heading',
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR') or is_granted('ROLE_MEMBER')")
     * @Route("/user/applications/delete", name="back.user.application.delete", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response
    {
        if ($this->isCsrfTokenValid('user_application_delete', $request->request->get('token'))) {
            $application = $this->applicationService->getApplicationById(intval($request->request->get('application')));
            if ($application) {
                $this->applicationService->deleteApplication($application);
            }
            return $this->redirectToRoute('back.user.application.list');
        }

        throw $this->createAccessDeniedException($this->translator->trans('common.access_denied', [], 'translations', $request->getLocale()));
    }

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR')")
     * @Route("/user/applications/all", name="back.user.application.all")
     * @param ApplicationService $applicationService
     * @return Response
     */
    public function all(ApplicationService $applicationService): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('back-office/pages/application/list.html.twig', [
            'title' => 'back.default.pages.applications.heading',
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
            'applications' => $applicationService->getApplications(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMINISTRATOR')")
     * @Route("/user/application/status/update", name="back.application.status.change", methods={"POST"})
     * @param Request $request
     * @param ApplicationService $applicationService
     * @return Response
     */
    public function changeStatus(Request $request, ApplicationService $applicationService): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($this->isCsrfTokenValid('change_application_status', $request->request->get('csrf_token'))) {
            $applicationId = intval($request->request->get('application'));
            $applicationStatus = ($request->request->get('status') === 'on') ? Application::STATUS_VALIDATED : Application::STATUS_REJECTED;

            $applicationService->changeApplicationStatus($applicationId, $applicationStatus);
            return $this->redirect($request->headers->get('referer'));
        }

        throw $this->createAccessDeniedException($this->translator->trans('common.access_denied', [], 'translations', $request->getLocale()));
    }

}
