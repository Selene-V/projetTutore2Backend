<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
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
    private $appid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="date")
     */
    private $releaseDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $english;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $developer;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $publisher;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $platforms;

    /**
     * @ORM\Column(type="integer")
     */
    private $requiredAge;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $categories;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $genres;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $steamspyTags;

    /**
     * @ORM\Column(type="integer")
     */
    private $achievements;

    /**
     * @ORM\Column(type="integer")
     */
    private $positiveRatings;

    /**
     * @ORM\Column(type="integer")
     */
    private $negativeRatings;

    /**
     * @ORM\Column(type="integer")
     */
    private $averagePlaytime;

    /**
     * @ORM\Column(type="integer")
     */
    private $medianPlaytime;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $owners;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    private $image;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAppid(): ?int
    {
        return $this->appid;
    }

    public function setAppid(int $appid): self
    {
        $this->appid = $appid;

        return $this;
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

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTimeInterface $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getEnglish(): ?int
    {
        return $this->english;
    }

    public function setEnglish(int $english): self
    {
        $this->english = $english;

        return $this;
    }

    public function getDeveloper(): ?string
    {
        return $this->developer;
    }

    public function setDeveloper(string $developer): self
    {
        $this->developer = $developer;

        return $this;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(string $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function getPlatforms(): ?string
    {
        return $this->platforms;
    }

    public function setPlatforms(string $platforms): self
    {
        $this->platforms = $platforms;

        return $this;
    }

    public function getRequiredAge(): ?int
    {
        return $this->requiredAge;
    }

    public function setRequiredAge(int $requiredAge): self
    {
        $this->requiredAge = $requiredAge;

        return $this;
    }

    public function getCategories(): ?string
    {
        return $this->categories;
    }

    public function setCategories(string $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function getGenres(): ?string
    {
        return $this->genres;
    }

    public function setGenres(string $genres): self
    {
        $this->genres = $genres;

        return $this;
    }

    public function getSteamspyTags(): ?string
    {
        return $this->steamspyTags;
    }

    public function setSteamspyTags(string $steamspyTags): self
    {
        $this->steamspyTags = $steamspyTags;

        return $this;
    }

    public function getAchievements(): ?int
    {
        return $this->achievements;
    }

    public function setAchievements(int $achievements): self
    {
        $this->achievements = $achievements;

        return $this;
    }

    public function getPositiveRatings(): ?int
    {
        return $this->positiveRatings;
    }

    public function setPositiveRatings(int $positiveRatings): self
    {
        $this->positiveRatings = $positiveRatings;

        return $this;
    }

    public function getNegativeRatings(): ?int
    {
        return $this->negativeRatings;
    }

    public function setNegativeRatings(int $negativeRatings): self
    {
        $this->negativeRatings = $negativeRatings;

        return $this;
    }

    public function getAveragePlaytime(): ?int
    {
        return $this->averagePlaytime;
    }

    public function setAveragePlaytime(int $averagePlaytime): self
    {
        $this->averagePlaytime = $averagePlaytime;

        return $this;
    }

    public function getMedianPlaytime(): ?int
    {
        return $this->medianPlaytime;
    }

    public function setMedianPlaytime(int $medianPlaytime): self
    {
        $this->medianPlaytime = $medianPlaytime;

        return $this;
    }

    public function getOwners(): ?string
    {
        return $this->owners;
    }

    public function setOwners(string $owners): self
    {
        $this->owners = $owners;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image): self
    {
        $this->image = $image;
        return $this;
    }


}
