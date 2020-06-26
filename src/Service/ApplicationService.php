<?php

namespace App\Service;

use App\Entity\Application;
use App\Repository\ApplicationRepository;
use Doctrine\ORM\EntityManagerInterface;

class ApplicationService
{
    private $entityManager;
    private $applicationRepository;

    public function __construct(EntityManagerInterface $entityManager, ApplicationRepository $applicationRepository)
    {
        $this->entityManager = $entityManager;
        $this->applicationRepository = $applicationRepository;
    }

    public function getApplicationById(int $id): ?Application
    {
        return $this->applicationRepository->findOneBy([
            'id' => $id
        ]);
    }

    public function getApplications(string $status = null): array
    {
        if ($status) {
            return $this->applicationRepository->findBy([
                'status' => $status
            ]);
        }

        return $this->applicationRepository->findAll();
    }


    public function saveApplication(Application $application)
    {
        $this->entityManager->persist($application);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function deleteApplication(Application $application)
    {
        $this->entityManager->remove($application);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function changeApplicationStatus(int $applicationId, string $status)
    {
        $queryBuilder = $this->applicationRepository->createQueryBuilder('application');
        $queryBuilder
            ->update(Application::class, 'application')
            ->set('application.status', ':status')
            ->where('application.id = :id')
            ->setParameter('status', $status)
            ->setParameter('id', $applicationId);

        $queryBuilder->getQuery()->execute();
    }
}
