<?php

namespace App\Entity;

use App\Repository\DescriptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DescriptionRepository::class)
 */
class Description
{
    use HydrationTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $steamAppid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $detailedDescription;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $aboutTheGame;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $shortDescription;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSteamAppid(): ?int
    {
        return $this->steamAppid;
    }

    public function setSteamAppid(int $steamAppid): self
    {
        $this->steamAppid = $steamAppid;

        return $this;
    }

    public function getDetailedDescription(): ?string
    {
        return $this->detailedDescription;
    }

    public function setDetailedDescription(string $detailedDescription): self
    {
        $this->detailedDescription = $detailedDescription;

        return $this;
    }

    public function getAboutTheGame(): ?string
    {
        return $this->aboutTheGame;
    }

    public function setAboutTheGame(string $aboutTheGame): self
    {
        $this->aboutTheGame = $aboutTheGame;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }
}
