<?php


namespace App\Controller\Back;

use App\Entity\User;
use App\Form\UserType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMINISTRATOR') or is_granted('ROLE_MEMBER') or is_granted('ROLE_CANDIDATE')")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("user/profile", name="back.user.profile")
     * @param Request $request
     * @param UserService $userService
     * @return Response
     */
    public function edit (Request $request, UserService $userService): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $userClone = clone $user;

        $form = $this->createForm(UserType::class, $user, ['edit_mode' => true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            $userService->authenticateUser($request, $userClone);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userService->saveUser(clone $user);
            return $userService->authenticateUser($request, $user);
        }

        return $this->render('back-office/pages/profile.html.twig', [
            'title' => 'back.default.pages.profile.heading',
            'user' => $user,
            'notification' => $userService->getUserNotification($user),
            'form' => $form->createView()
        ]);
    }
}