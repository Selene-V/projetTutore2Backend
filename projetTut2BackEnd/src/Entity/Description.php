<?php

namespace App\Entity;

class Description
{
    use HydrationTrait;

    private ?string $id = null;

    private ?int $steamAppid = null;

    private ?string $detailedDescription = null;

    private ?string $aboutTheGame = null;

    private ?string $shortDescription = null;

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
    public function getDetailedDescription(): ?string
    {
        return $this->detailedDescription;
    }

    /**
     * @param string $detailedDescription
     * @return $this
     */
    public function setDetailedDescription(string $detailedDescription): self
    {
        $this->detailedDescription = $detailedDescription;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAboutTheGame(): ?string
    {
        return $this->aboutTheGame;
    }

    /**
     * @param string $aboutTheGame
     * @return $this
     */
    public function setAboutTheGame(string $aboutTheGame): self
    {
        $this->aboutTheGame = $aboutTheGame;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    /**
     * @param string $shortDescription
     * @return $this
     */
    public function setShortDescription(string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }
}
