<?php

namespace App\Service;

use App\Entity\Application;
use App\Entity\User;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class UserService
{
    private $entityManager;
    private $userRepository;
    private $fileUploader;
    private $passwordEncoder;
    private $guardHandler;
    private $formAuthenticator;
    private $applicationService;


    public function __construct(
        EntityManagerInterface $entityManager, FileUploader $fileUploader, UserPasswordEncoderInterface $encoder,
        GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $formAuthenticator, ApplicationService $applicationService)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $entityManager->getRepository(User::class);
        $this->fileUploader = $fileUploader;
        $this->passwordEncoder = $encoder;
        $this->guardHandler = $guardHandler;
        $this->formAuthenticator = $formAuthenticator;
        $this->applicationService = $applicationService;
    }

    public function authenticateUser(Request $request, UserInterface $user)
    {
        return $this->guardHandler->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $this->formAuthenticator,
            'main'
        );
    }

    public function persistUser(User $user)
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function refreshUser(User $user)
    {
        $this->entityManager->refresh($user);
    }

    public function getUserById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->userRepository->findOneBy([
            'email' => $email
        ]);
    }

    public function getUserNotification(User $user): ?object
    {
        if (in_array(User::ROLE_ADMINISTRATOR, $user->getRoles())) {
            $pendingUsers = count($this->userRepository->findBy([
                'active' => false
            ]));

            $pendingApplications = count(
                $this->applicationService->getApplications(Application::STATUS_PENDING)
            );

            return (object)[
                'total' => ($pendingUsers + $pendingApplications),
                'members' => $pendingUsers,
                'applications' => $pendingApplications,
            ];
        }

        return null;
    }

    public function saveUser(User $user)
    {
        $userBdd = $this->getUserByEmail($user->getEmail()) ?? new User();
        $this->entityManager->refresh($userBdd);
        $userBdd
            ->setLastName($user->getLastName())
            ->setFirstName($user->getFirstName());

        if ($user->getPassword() !== null) {
            $userBdd->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
        }

        if ($user->getAvatar() !== null) {
            $userBdd->setAvatar($this->fileUploader->uploadAvatar($user->getAvatar()));
        }

        $this->persistUser($userBdd);
        return $userBdd;
    }

    public function getUsers(): array
    {
        return $this->userRepository->findAll();
    }

    public function getUsersByStatus(bool $active): array
    {
        return $this->userRepository->findBy([
            'active' => $active
        ]);
    }

    public function getUsersByRole(string $role): array
    {
        $queryBuilder = $this->userRepository->createQueryBuilder('user');
        $queryBuilder
            ->select('user')
            ->where('user.roles LIKE :role')
            ->setParameter('role', '%' . $role . '%');
        return $queryBuilder->getQuery()->getResult();
    }

    public function changeUserStatus(int $userId, bool $active)
    {
        $queryBuilder = $this->userRepository->createQueryBuilder('user');
        $queryBuilder
            ->update(User::class, 'user')
            ->set('user.active', ':active')
            ->where('user.id = :id')
            ->setParameter('active', $active)
            ->setParameter('id', $userId);

        $queryBuilder->getQuery()->execute();
    }
}
