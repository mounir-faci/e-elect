<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *     fields={"email"},
 *     message="user.email.exists"
 * )
 */
class User implements UserInterface
{
    public const ROLE_MEMBER = 'ROLE_MEMBER';
    public const ROLE_ADMINISTRATOR = 'ROLE_ADMINISTRATOR';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="8", minMessage="user.password.length")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\File(mimeTypes={"image/jpeg", "image/png"}, mimeTypesMessage="user.avatar.mime_type")
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Length(
     *     min="3", minMessage="user.names.min_length",
     *     max="50", maxMessage="user.names.max_length"
     * )
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Length(
     *     min="3", minMessage="user.names.min_length",
     *     max="50", maxMessage="user.names.max_length"
     * )
     */
    private $lastName;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @var string $passwordConfirmation
     * @Assert\EqualTo(
     *     propertyPath="password",
     *     message="user.password.confirm"
     * )
     */
    private $passwordConfirmation;

    /**
     * @ORM\OneToMany(targetEntity=Vote::class, mappedBy="voter")
     */
    private $votes;

    /**
     * @ORM\OneToMany(targetEntity=Application::class, mappedBy="candidate")
     */
    private $applications;

    public function __construct()
    {
        $this->votes = new ArrayCollection();
        $this->applications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getPasswordConfirmation(): ?string
    {
        return $this->passwordConfirmation;
    }

    public function setPasswordConfirmation(?string $passwordConfirmation): self
    {
        $this->passwordConfirmation = $passwordConfirmation;
        return $this;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes[] = $vote;
            $vote->setCandidate($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->votes->contains($vote)) {
            $this->votes->removeElement($vote);
            // set the owning side to null (unless already changed)
            if ($vote->getCandidate() === $this) {
                $vote->setCandidate(null);
            }
        }

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
            $application->setCandidate($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): self
    {
        if ($this->applications->contains($application)) {
            $this->applications->removeElement($application);
            // set the owning side to null (unless already changed)
            if ($application->getCandidate() === $this) {
                $application->setCandidate(null);
            }
        }

        return $this;
    }

    public function isMember(): bool
    {
        return in_array(self::ROLE_MEMBER, $this->getRoles());
    }

    public function isAdministrator(): bool
    {
        return in_array(self::ROLE_ADMINISTRATOR, $this->getRoles());
    }
}
