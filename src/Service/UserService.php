<?php


namespace App\Service;

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


    public function __construct(
        EntityManagerInterface $entityManager, FileUploader $fileUploader, UserPasswordEncoderInterface $encoder,
        GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $formAuthenticator)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $entityManager->getRepository(User::class);
        $this->fileUploader = $fileUploader;
        $this->passwordEncoder = $encoder;
        $this->guardHandler = $guardHandler;
        $this->formAuthenticator = $formAuthenticator;
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
            return (object) [
                'total' => 15,
                'members' => 10,
                'candidates' => 5,
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

        if($user->getPassword() !== null) {
            $userBdd->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
        }

        if($user->getAvatar() !== null) {
            $userBdd->setAvatar($this->fileUploader->uploadAvatar($user->getAvatar()));
        }

        $this->persistUser($userBdd);
        return $userBdd;
    }
}
