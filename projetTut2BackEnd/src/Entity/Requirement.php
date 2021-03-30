<?php

namespace App\Entity;

class Requirement
{
    use HydrationTrait;

    private ?string $id = null;

    private ?int $steamAppid = null;

    private ?string $pcRequirements = null;

    private ?string $macRequirements = null;

    private ?string $linuxRequirements = null;

    private ?string $minimum = null;

    private ?string $recommended = null;

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
    public function getPcRequirements(): ?string
    {
        return $this->pcRequirements;
    }

    /**
     * @param string $pcRequirements
     * @return $this
     */
    public function setPcRequirements(string $pcRequirements): self
    {
        $this->pcRequirements = $pcRequirements;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMacRequirements(): ?string
    {
        return $this->macRequirements;
    }

    /**
     * @param string $macRequirements
     * @return $this
     */
    public function setMacRequirements(string $macRequirements): self
    {
        $this->macRequirements = $macRequirements;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLinuxRequirements(): ?string
    {
        return $this->linuxRequirements;
    }

    /**
     * @param string $linuxRequirements
     * @return $this
     */
    public function setLinuxRequirements(string $linuxRequirements): self
    {
        $this->linuxRequirements = $linuxRequirements;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMinimum(): ?string
    {
        return $this->minimum;
    }

    /**
     * @param string $minimum
     * @return $this
     */
    public function setMinimum(string $minimum): self
    {
        $this->minimum = $minimum;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRecommended(): ?string
    {
        return $this->recommended;
    }

    /**
     * @param string|null $recommended
     * @return $this
     */
    public function setRecommended(?string $recommended): self
    {
        $this->recommended = $recommended;

        return $this;
    }
}
