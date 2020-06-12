<?php


namespace App\Service;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var UserRepository $userRepository
     */
    private $userRepository;

    /**
     * @var PasswordEncoderInterface $passwordEncoder
     */
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordEncoderInterface $encoder)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $encoder;
    }

    public function saveUser(User $user)
    {
        $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
