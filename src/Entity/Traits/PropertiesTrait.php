<?php

namespace App\Entity\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait PropertiesTrait
{
    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private DateTime $createdAt;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private DateTime $updatedAt;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $guardImageName;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $illustrativeImageName;


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

    /**
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new DateTime();

        return $this;
    }

    /**
     * @param null|string $guardImageName
     */
    public function setGuardImageName(?string $guardImageName): void
    {
        $this->guardImageName = $guardImageName;
    }

    /**
     * @return null|string
     */
    public function getGuardImageName(): ?string
    {
        return $this->guardImageName;
    }



    /**
     * @param null|string $illustrativeImageName
     */
    public function setIllustrativeImageName(?string $illustrativeImageName): void
    {
        $this->illustrativeImageName = $illustrativeImageName;
    }

    /**
     * @return null|string
     */
    public function getIllustrativeImageName(): ?string
    {
        return $this->illustrativeImageName;
    }
}
