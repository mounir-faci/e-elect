<?php

namespace App\DataFixtures;

use App\Entity\Application;
use App\Entity\Election;
use App\Entity\User;
use App\Entity\Vote;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;

class AppFixtures extends Fixture
{
    private const LOCALE = 'fr_FR';
    private const DEFAULT_PASSWORD = '123456789';
    private $encoder;
    private $faker;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->faker = FakerFactory::create(self::LOCALE);
    }

    public function load(ObjectManager $manager)
    {
        // Create admin
        $admin = $this->loadAdmin($manager);

        // Create fake users
        $users = $this->loadUsers($manager);

        // Create fake elections
        $elections = $this->loadElections($manager);

        // Create fake applications
        $applications = $this->loadApplications($manager, $users, $elections);

        // Create fake votes
        $this->loadVotes($manager, $applications, $users, $admin);

    }

    private function loadAdmin(ObjectManager $manager): User
    {
        $admin = new User();
        $admin
            ->setAvatar('avatar-default.png')
            ->setFirstName('Mounir')
            ->setLastName('FACI')
            ->setEmail('faci.mounir@gmail.com')
            ->setPassword($this->encoder->encodePassword($admin, self::DEFAULT_PASSWORD))
            ->setRoles(['ROLE_ADMINISTRATOR'])
            ->setActive(true)
        ;

        $manager->persist($admin);
        $manager->flush();

        return $admin;
    }

    private function loadUsers(ObjectManager $manager): array
    {
        $users = [];
        for ($cpt = 0; $cpt < 100; $cpt++) {
            $user = new User();
            $user
                ->setAvatar('avatar-default.png')
                ->setFirstName($this->faker->firstName)
                ->setLastName($this->faker->lastName)
                ->setEmail($this->faker->email)
                ->setPassword($this->encoder->encodePassword($user, self::DEFAULT_PASSWORD))
                ->setRoles(['ROLE_MEMBER'])
                ->setActive(true)
            ;

            $manager->persist($user);
            $users[$cpt] = $user;
        }

        for ($cpt = 0; $cpt < 10; $cpt++) {
            $user = new User();
            $user
                ->setAvatar('avatar-default.png')
                ->setFirstName($this->faker->firstName)
                ->setLastName($this->faker->lastName)
                ->setEmail($this->faker->email)
                ->setPassword($this->encoder->encodePassword($user, self::DEFAULT_PASSWORD))
                ->setRoles(['ROLE_MEMBER'])
                ->setActive(false)
            ;

            $manager->persist($user);
        }

        // persist data in database
        $manager->flush();
        return $users;
    }

    private function loadElections(ObjectManager $manager): array
    {
        $elections = [];
        for ($cpt = 0; $cpt < 20; $cpt++) {
            $election = new Election();
            $election
                ->setName($this->faker->title)
                ->setDescription($this->faker->realText( 800, 4))
                ->setStartDate($this->faker->dateTime)
                ->setEndDate($this->faker->dateTime)
                ->setStatus(Election::STATUS_PENDING)
            ;

            $manager->persist($election);
            $elections[$cpt] = $election;
        }

        for ($cpt = 0; $cpt < 10; $cpt++) {
            $election = new Election();
            $election
                ->setName($this->faker->title)
                ->setDescription($this->faker->realText( 800, 4))
                ->setStartDate($this->faker->dateTime)
                ->setEndDate($this->faker->dateTime)
                ->setStatus(Election::STATUS_CREATED)
            ;

            $manager->persist($election);
        }

        // persist data in database
        $manager->flush();

        return $elections;
    }

    private function loadApplications(ObjectManager $manager, array $users, array $elections): array
    {
        $applications = [];
        for ($cpt = 0; $cpt < 40; $cpt++) {
            $application = new Application();
            $application
                ->setCandidate($users[$cpt+1])
                ->setElection($elections[$cpt % 20])
                ->setContent($this->faker->realText(1200, 5))
                ->setStatus(Application::STATUS_PENDING)
            ;

            $manager->persist($application);
            $applications[$cpt] = $application;
        }

        // persist data in database
        $manager->flush();

        return $applications;
    }

    private function loadVotes(ObjectManager $manager, array $applications, array $users, User $admin)
    {
        for ($cpt = 0; $cpt < 90; $cpt++) {
            $vote = new Vote();
            $vote
                ->setApplication($applications[$cpt % 40])
                ->setVoter($users[$cpt])
                ->setVoteDate($this->faker->dateTime)
            ;

            if ($cpt % 20) {
                $vote->setVoter($admin);
            }

            $manager->persist($vote);
        }

        // persist data in database
        $manager->flush();
    }
}
