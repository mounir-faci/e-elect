<?php


namespace App\Service;


use App\Repository\ElectionRepository;

class ElectionService
{
    private $electionRepository;

    public function __construct(ElectionRepository $electionRepository)
    {
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
}