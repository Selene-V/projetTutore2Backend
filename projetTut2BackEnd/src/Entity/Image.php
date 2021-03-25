<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

class Image
{
    use HydrationTrait;

    private ?string $id = null;

    private ?int $steamAppid = null;

    private ?string $headerImage = null;

    private ?string $screenshots = null;

    private ?string $background = null;

    private ?string $movies = null;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSteamAppid(): ?int
    {
        return $this->steamAppid;
    }

    /**
     * @param int $steamAppid
     * @return $this
     */
    public function setSteamAppid(int $steamAppid): self
    {
        $this->steamAppid = $steamAppid;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHeaderImage(): ?string
    {
        return $this->headerImage;
    }

    /**
     * @param string $headerImage
     * @return $this
     */
    public function setHeaderImage(string $headerImage): self
    {
        $this->headerImage = $headerImage;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getScreenshots(): ?string
    {
        return $this->screenshots;
    }

    /**
     * @param string $screenshots
     * @return $this
     */
    public function setScreenshots(string $screenshots): self
    {
        $this->screenshots = $screenshots;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBackground(): ?string
    {
        return $this->background;
    }

    /**
     * @param string $background
     * @return $this
     */
    public function setBackground(string $background): self
    {
        $this->background = $background;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMovies(): ?string
    {
        return $this->movies;
    }

    /**
     * @param string $movies
     * @return $this
     */
    public function setMovies(string $movies): self
    {
        $this->movies = $movies;

        return $this;
    }
}
