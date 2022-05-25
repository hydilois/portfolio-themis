<?php

namespace App\Entity;

use App\Repository\MessagesRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MessagesRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Messages
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=3, minMessage="Votre nom doit contenir au moins 3 caractères")
     */
    private $fullName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="Vous devez renseigner un adresse email valide")
     */
    private $email;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=10, minMessage="Votre message doit avoir au moins 10 caractères")
     */
    private $content;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private DateTime $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAt()
    {
        $this->createdAt = new DateTime();

        return $this;
    }
}
