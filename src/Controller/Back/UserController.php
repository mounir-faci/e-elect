<?php


namespace App\Controller\Back;

use App\Entity\User;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Security("is_granted('ROLE_ADMINISTRATOR')")
 */
class UserController extends AbstractController
{
    private $userService;
    private $translator;

    public function __construct(UserService $userService, TranslatorInterface $translator)
    {
        $this->userService = $userService;
        $this->translator = $translator;
    }

    /**
     * @Route("/admin/users/all", name="back.users.list")
     */
    public function all ()
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('back-office/pages/users.html.twig', [
            'title' => 'back.default.pages.users.list.heading',
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
            'users' => $this->userService->getUsers(),
        ]);
    }

    /**
     * @Route("/admin/users/candidates", name="back.users.candidates")
     */
    public function candidates ()
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('back-office/pages/users.html.twig', [
            'title' => 'back.default.pages.users.candidates.heading',
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
            'users' => $this->userService->getUsersByRole(User::ROLE_CANDIDATE),
        ]);
    }

    /**
     * @Route("/admin/users/members", name="back.users.members")
     */
    public function members ()
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('back-office/pages/users.html.twig', [
            'title' => 'back.default.pages.users.members.heading',
            'user' => $user,
            'notification' => $this->userService->getUserNotification($user),
            'users' => $this->userService->getUsersByRole(User::ROLE_MEMBER),
        ]);
    }

    /**
     * @Route("/admin/users/status/change", name="back.users.status.change", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function changeStatus(Request $request): Response
    {
        if ($this->isCsrfTokenValid('change_user_status', $request->request->get('csrf_token'))) {
            $this->userService->changeUserStatus(
                intval($request->request->get('user')),
                $request->request->get('status') === 'on'
            );
            return $this->redirect($request->headers->get('referer'));
        }

        throw $this->createAccessDeniedException($this->translator->trans('common.access_denied', [], 'translations', $request->getLocale()));
    }
}