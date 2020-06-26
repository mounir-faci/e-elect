<?php


namespace App\Service;


use App\Entity\Vote;
use App\Repository\VoteRepository;
use Doctrine\ORM\EntityManagerInterface;

class VoteService
{
    private $entityManager;
    private $voteRepository;

    public function __construct(EntityManagerInterface $entityManager, VoteRepository $voteRepository)
    {
        $this->entityManager = $entityManager;
        $this->voteRepository = $voteRepository;
    }

    public function saveVote(Vote $vote)
    {
        $this->entityManager->persist($vote);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
