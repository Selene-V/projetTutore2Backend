<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
class Image
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
    private $headerImage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $screenshots;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $background;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $movies;

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

    public function getHeaderImage(): ?string
    {
        return $this->headerImage;
    }

    public function setHeaderImage(string $headerImage): self
    {
        $this->headerImage = $headerImage;

        return $this;
    }

    public function getScreenshots(): ?string
    {
        return $this->screenshots;
    }

    public function setScreenshots(string $screenshots): self
    {
        $this->screenshots = $screenshots;

        return $this;
    }

    public function getBackground(): ?string
    {
        return $this->background;
    }

    public function setBackground(string $background): self
    {
        $this->background = $background;

        return $this;
    }

    public function getMovies(): ?string
    {
        return $this->movies;
    }

    public function setMovies(string $movies): self
    {
        $this->movies = $movies;

        return $this;
    }
}
