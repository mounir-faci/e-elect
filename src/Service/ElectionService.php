<?php


namespace App\Service;


use App\Entity\Election;
use App\Repository\ElectionRepository;
use Doctrine\ORM\EntityManagerInterface;

class ElectionService
{
    private $entityManager;
    private $electionRepository;

    public function __construct(EntityManagerInterface $entityManager, ElectionRepository $electionRepository)
    {
        $this->entityManager = $entityManager;
        $this->electionRepository = $electionRepository;
    }

    public function getElections(string $status = null): array
    {
        if ($status == null) {
            return $this->electionRepository->findAll();
        }

        return $this->electionRepository->findBy([
            'status' => $status
        ]);
    }

    public function getElectionById(int $id): ?Election
    {
        return $this->electionRepository->findOneBy([
            'id' => $id
        ]);
    }

    public function saveElection(Election $election)
    {
        $this->entityManager->persist($election);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}