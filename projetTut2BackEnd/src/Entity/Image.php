<?php

namespace App\Entity;


class Image
{
    use HydrationTrait;

    private ?string $id = null;

    private ?int $steamAppid = null;

    private ?string $headerImage = null;

    private ?array $screenshots = null;

    private ?string $background = null;

    private ?array $movies = null;

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
     * @return array|null
     */
    public function getScreenshots(): ?array
    {
        return $this->screenshots;
    }

    /**
     * @param array|null $screenshots
     * @return $this
     */
    public function setScreenshots(?array $screenshots): self
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
     * @return array|null
     */
    public function getMovies(): ?array
    {
        return $this->movies;
    }

    /**
     * @param array|null $movies
     * @return $this
     */
    public function setMovies(?array $movies): self
    {
        $this->movies = $movies;

        return $this;
    }
}
