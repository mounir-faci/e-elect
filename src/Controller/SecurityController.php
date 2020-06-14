<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($this->getUser()) {
            return $this->redirectToRoute('back.dashboard');
        }

        return $this->render('back-office/pages/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {

    }

    /**
     * @Route("/register/member", name="register.member")
     * @param Request $request
     * @return Response
     */
    public function registerMember(Request $request): Response
    {
        return $this->register($request, [User::ROLE_MEMBER]);
    }

    /**
     * @Route("/register/candidate", name="register.candidate")
     * @param Request $request
     * @return Response
     */
    public function registerCandidate(Request $request): Response
    {
        return $this->register($request, [User::ROLE_CANDIDATE]);
    }

    private function register(Request $request, array $roles): Response
    {
        $user = new User();
        $user
            ->setLastName(strtoupper($user->getLastName()))
            ->setRoles($roles)
            ->setActive(false);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->saveUser($user);
            return $this->userService->authenticateUser($request, $user);
        }
        return $this->render('back-office/pages/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
