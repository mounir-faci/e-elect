<?php

namespace App\Entity;

use App\Repository\ElectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ElectionRepository::class)
 */
class Election
{
    public const STATUS_CREATED = 'STATUS_CREATED';
    public const STATUS_PENDING = 'STATUS_PENDING';
    public const STATUS_FINISHED = 'STATUS_FINISHED';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min="10", minMessage="election.length.min",
     *     max="255", maxMessage="election.length.max"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(
     *     min="10", minMessage="election.length.min",
     *     max="255", maxMessage="election.length.max"
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endDate;

    /**
     * @ORM\OneToMany(targetEntity=Application::class, mappedBy="election")
     */
    private $applications;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;


    public function __construct()
    {
        $this->applications = new ArrayCollection();
        $this->status = self::STATUS_CREATED;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications[] = $application;
            $application->setElection($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): self
    {
        if ($this->applications->contains($application)) {
            $this->applications->removeElement($application);
            // set the owning side to null (unless already changed)
            if ($application->getElection() === $this) {
                $application->setElection(null);
            }
        }

        return $this;
    }

    public function getTotalVotesCount (): int
    {
        return array_sum(
            array_map(
                function(Application $application) {
                    return $application->getVotes()->count();
                },
                $this->getApplications()->getValues()
            )
        );
    }
}
