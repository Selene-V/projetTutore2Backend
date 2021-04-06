<?php

namespace App\Entity;

use DateTime;

class Game
{
    use HydrationTrait;

    private ?string $id = null;

    private ?int $appid = null;

    private ?string $name = null;

    private ?DateTime $releaseDate = null;

    private ?int $english = null;

    private ?array $developer = null;

    private ?array $publisher = null;

    private ?array $platforms = null;

    private ?int $requiredAge = null;

    private ?array $categories = null;

    private ?array $genres = null;

    private ?array $steamspyTags = null;

    private ?int $achievements = null;

    private ?int $positiveRatings = null;

    private ?int $negativeRatings = null;

    private ?int $averagePlaytime = null;

    private ?int $medianPlaytime = null;

    private ?string $owners = null;

    private ?float $price = null;

    private ?Image $image = null;

    private ?Description $description = null;

    private ?Requirement $requirement = null;

    private ?array $tagCloud = null;

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
     * @return string
     */
    public function getReleaseDate(): string
    {
        return $this->releaseDate->format('Y-m-d');
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
     * @return array|null
     */
    public function getDeveloper(): ?array
    {
        return $this->developer;
    }

    /**
     * @param array $developer
     * @return $this
     */
    public function setDeveloper(array $developer): self
    {
        $this->developer = $developer;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getPublisher(): ?array
    {
        return $this->publisher;
    }

    /**
     * @param array $publisher
     * @return $this
     */
    public function setPublisher(array $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getPlatforms(): ?array
    {
        return $this->platforms;
    }

    /**
     * @param array $platforms
     * @return $this
     */
    public function setPlatforms(array $platforms): self
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
     * @return array|null
     */
    public function getCategories(): ?array
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     * @return $this
     */
    public function setCategories(array $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getGenres(): ?array
    {
        return $this->genres;
    }

    /**
     * @param array $genres
     * @return $this
     */
    public function setGenres(array $genres): self
    {
        $this->genres = $genres;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getSteamspyTags(): ?array
    {
        return $this->steamspyTags;
    }

    /**
     * @param array $steamspyTags
     * @return $this
     */
    public function setSteamspyTags(array $steamspyTags): self
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

    /**
     * @return Requirement|null
     */
    public function getRequirement(): ?Requirement
    {
        return $this->requirement;
    }

    /**
     * @param Requirement|null $requirement
     * @return $this
     */
    public function setRequirement(?Requirement $requirement): self
    {
        $this->requirement = $requirement;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getTagCloud(): ?array
    {
        return $this->tagCloud;
    }

    /**
     * @param array|null $tagCloud
     * @return Game
     */
    public function setTagCloud(?array $tagCloud): self
    {
        $this->tagCloud = $tagCloud;

        return $this;
    }
}
