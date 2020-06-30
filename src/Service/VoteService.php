<?php


namespace App\Service;
use App\Entity\User;
use App\Entity\Vote;
use Doctrine\ORM\EntityManagerInterface;

class VoteService
{
    private $entityManager;
    private $voteRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->voteRepository = $entityManager->getRepository(Vote::class);
    }

    public function saveVote(Vote $vote)
    {
        $this->entityManager->persist($vote);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function getVotes(): array
    {
        return $this->voteRepository->findAll();
    }

    public function getUserReceivedVotes(User $user): array
    {
        $queryBuilder = $this->voteRepository->createQueryBuilder('vote');
        $queryBuilder
            ->select('vote')
            ->join('vote.application', 'application')
            ->where('application.candidate = :user')
            ->setParameter('user', $user)
        ;
        return $queryBuilder->getQuery()->getResult();
    }

}
