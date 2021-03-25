<?php

namespace App\Entity;

use App\Repository\GameRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;


class Game
{
    use HydrationTrait;

    private ?string $id = null;

    private ?int $appid = null;

    private ?string $name = null;

    private ?DateTime $releaseDate = null;

    private ?int $english = null;

    private ?string $developer = null;

    private ?string $publisher = null;

    private ?string $platforms = null;

    private ?int $requiredAge = null;

    private ?string $categories = null;

    private ?string $genres = null;

    private ?string $steamspyTags = null;

    private ?int $achievements = null;

    private ?int $positiveRatings = null;

    private ?int $negativeRatings = null;

    private ?int $averagePlaytime = null;

    private ?int $medianPlaytime = null;

    private ?string $owners = null;

    private ?float $price = null;

    private ?Image $image = null;

    private ?Description $description = null;

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
    public function getAppid(): ?int
    {
        return $this->appid;
    }

    /**
     * @param int $appid
     * @return $this
     */
    public function setAppid(int $appid): self
    {
        $this->appid = $appid;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getReleaseDate(): ?DateTime
    {
        return $this->releaseDate;
    }

    /**
     * @param DateTime $releaseDate
     * @return $this
     */
    public function setReleaseDate(DateTime $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getEnglish(): ?int
    {
        return $this->english;
    }

    /**
     * @param int $english
     * @return $this
     */
    public function setEnglish(int $english): self
    {
        $this->english = $english;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDeveloper(): ?string
    {
        return $this->developer;
    }

    /**
     * @param string $developer
     * @return $this
     */
    public function setDeveloper(string $developer): self
    {
        $this->developer = $developer;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    /**
     * @param string $publisher
     * @return $this
     */
    public function setPublisher(string $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlatforms(): ?string
    {
        return $this->platforms;
    }

    /**
     * @param string $platforms
     * @return $this
     */
    public function setPlatforms(string $platforms): self
    {
        $this->platforms = $platforms;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getRequiredAge(): ?int
    {
        return $this->requiredAge;
    }

    /**
     * @param int $requiredAge
     * @return $this
     */
    public function setRequiredAge(int $requiredAge): self
    {
        $this->requiredAge = $requiredAge;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCategories(): ?string
    {
        return $this->categories;
    }

    /**
     * @param string $categories
     * @return $this
     */
    public function setCategories(string $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGenres(): ?string
    {
        return $this->genres;
    }

    /**
     * @param string $genres
     * @return $this
     */
    public function setGenres(string $genres): self
    {
        $this->genres = $genres;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSteamspyTags(): ?string
    {
        return $this->steamspyTags;
    }

    /**
     * @param string $steamspyTags
     * @return $this
     */
    public function setSteamspyTags(string $steamspyTags): self
    {
        $this->steamspyTags = $steamspyTags;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAchievements(): ?int
    {
        return $this->achievements;
    }

    /**
     * @param int $achievements
     * @return $this
     */
    public function setAchievements(int $achievements): self
    {
        $this->achievements = $achievements;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPositiveRatings(): ?int
    {
        return $this->positiveRatings;
    }

    /**
     * @param int $positiveRatings
     * @return $this
     */
    public function setPositiveRatings(int $positiveRatings): self
    {
        $this->positiveRatings = $positiveRatings;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNegativeRatings(): ?int
    {
        return $this->negativeRatings;
    }

    /**
     * @param int $negativeRatings
     * @return $this
     */
    public function setNegativeRatings(int $negativeRatings): self
    {
        $this->negativeRatings = $negativeRatings;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAveragePlaytime(): ?int
    {
        return $this->averagePlaytime;
    }

    /**
     * @param int $averagePlaytime
     * @return $this
     */
    public function setAveragePlaytime(int $averagePlaytime): self
    {
        $this->averagePlaytime = $averagePlaytime;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMedianPlaytime(): ?int
    {
        return $this->medianPlaytime;
    }

    /**
     * @param int $medianPlaytime
     * @return $this
     */
    public function setMedianPlaytime(int $medianPlaytime): self
    {
        $this->medianPlaytime = $medianPlaytime;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOwners(): ?string
    {
        return $this->owners;
    }

    /**
     * @param string $owners
     * @return $this
     */
    public function setOwners(string $owners): self
    {
        $this->owners = $owners;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return $this
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Image|null
     */
    public function getImage(): ?Image
    {
        return $this->image;
    }

    /**
     * @param Image $image
     * @return $this
     */
    public function setImage(Image $image): self
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return Description|null
     */
    public function getDescription(): ?Description
    {
        return $this->description;
    }

    /**
     * @param Description $description
     * @return $this
     */
    public function setDescription(Description $description): self
    {
        $this->description = $description;
        return $this;
    }


}
