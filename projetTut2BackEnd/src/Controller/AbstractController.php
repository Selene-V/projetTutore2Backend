<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Description;
use App\Entity\Requirement;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AbstractController
{
    protected Client $client;
    private array $encoders;
    private array $normalizers;
    protected Serializer $serializer;

    /**
     * AbstractController constructor.
     */
    public function __construct()
    {
        $this->client = ClientBuilder::create()->setHosts(['localhost:9200'])->build();
        $this->encoders = [new XmlEncoder(), new JsonEncoder()];
        $this->normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($this->normalizers, $this->encoders);
    }

    /**
     * @param int $idGame
     * @return Image
     */
    protected function createImage(int $idGame): Image
    {
        $image = new Image();
        $imageData = json_decode($this->imagesByGame($idGame)->getContent(), true);
        if ($imageData['hits']['hits'] != null) {
            $imageData['hits']['hits'][0]['_source']['data']['screenshots'] = json_decode(str_replace("'", "\"", $imageData['hits']['hits'][0]['_source']['data']['screenshots']), true);
            $imageData['hits']['hits'][0]['_source']['data']['movies'] = str_replace("'", "\"", $imageData['hits']['hits'][0]['_source']['data']['movies']);
            $imageData['hits']['hits'][0]['_source']['data']['movies'] = str_replace("True", "true", $imageData['hits']['hits'][0]['_source']['data']['movies']);
            $imageData['hits']['hits'][0]['_source']['data']['movies'] = json_decode(str_replace("False", "false", $imageData['hits']['hits'][0]['_source']['data']['movies']), true);

            $image->hydrate($imageData['hits']['hits'][0]['_source']['data']);
            $image->setId($imageData['hits']['hits'][0]['_id']);
        }

        return $image;
    }

    /**
     * @param int $idGame
     * @return Description
     */
    protected function createDescription(int $idGame): Description
    {
        $description = new Description();
        $descriptionData = json_decode($this->descriptionsByGame($idGame)->getContent(), true);
        if ($descriptionData['hits']['hits'] != null) {
            $description->hydrate($descriptionData['hits']['hits'][0]['_source']['data']);
            $description->setId($descriptionData['hits']['hits'][0]['_id']);
        }

        return $description;
    }

    /**
     * @param int $idGame
     * @return Requirement
     */
    protected function createRequirement(int $idGame): Requirement
    {
        $requirement = new Requirement();
        $requirementData = json_decode($this->requirementsByGame($idGame)->getContent(), true);
        if ($requirementData['hits']['hits'] != null) {
            $requirement->hydrate($requirementData['hits']['hits'][0]['_source']['data']);
            $requirement->setId($requirementData['hits']['hits'][0]['_id']);
        }

        return $requirement;
    }

    /**
     * @param $sorting
     * @param $keywordArray
     * @return string[]
     */
    protected function setSorting($sorting, $keywordArray): array
    {
        $temp = explode('-', $sorting);
        $criteria = $temp[0];
        $order = $temp[1];

        if (in_array($criteria, $keywordArray)) {
            return array('data.' . $criteria . '.keyword:' . $order);
        } else {
            return array('data.' . $criteria . ':' . $order);
        }
    }

    /**
     * @param $specialParam
     * @return string
     */
    protected function handleSpecialParams($specialParam): string
    {
        $chars = str_split($specialParam);

        foreach ($chars as $key => $char) {
            if ($char === "~" && $key !== 0) {
                $chars[$key] = " ";
            }
        }
        return implode("", $chars);
    }

    /**
     * @param int $appid
     * @return JsonResponse
     */
    private function imagesByGame(int $appid): JsonResponse
    {
        $params = [
            'index' => 'steam_media_data',
            'body' => [
                'query' => [
                    'match' => [
                        'data.steam_appid' => $appid
                    ]
                ]
            ]
        ];

        $result = $this->client->search($params);

        return new JsonResponse($result);
    }

    /**
     * @param int $appid
     * @return JsonResponse
     */
    private function descriptionsByGame(int $appid): JsonResponse
    {
        $params = [
            'index' => 'steam_description_data',
            'body' => [
                'query' => [
                    'match' => [
                        'data.steam_appid' => $appid
                    ]
                ]
            ]
        ];

        $results = $this->client->search($params);

        return new JsonResponse($results);
    }

    /**
     * @param int $appid
     * @return JsonResponse
     */
    private function requirementsByGame(int $appid): JsonResponse
    {
        $params = [
            'index' => 'steam_requirements_data',
            'body' => [
                'query' => [
                    'match' => [
                        'data.steam_appid' => $appid
                    ]
                ]
            ]
        ];

        $results = $this->client->search($params);

        return new JsonResponse($results);
    }

    /**
     * @param int $appid
     * @return JsonResponse
     */
    public function tagCloud(int $appid): JsonResponse
    { {
            $params = [
                'index' => 'steamspy_tag_data',
                'body' => [
                    'query' => [
                        'match' => [
                            'data.appid' => $appid
                        ]
                    ]
                ]
            ];

            $results = $this->client->search($params);
            $tagsWeight = $results['hits']['hits'][0]['_source']['data'];
            unset($tagsWeight['appid']);

            $tags = [];
            foreach ($tagsWeight as $tag => $weight) {
                if ($weight !== 0) {
                    $tags[] = $tag;
                }
            }

            return new JsonResponse($tags);
        }
    }

    /**
     * @param int $appid
     * @return JsonResponse
     */
    public function tagWeightByGame(int $appid): JsonResponse
    {
        $params = [
            'index' => 'steamspy_tag_data',
            'body' => [
                'query' => [
                    'match' => [
                        'data.appid' => $appid
                    ]
                ]
            ]
        ];

        $results = $this->client->search($params);

        arsort($results['hits']['hits'][0]['_source']['data']);
        unset($results['hits']['hits'][0]['_source']['data']['appid']);

        foreach ($results['hits']['hits'][0]['_source']['data'] as $key => $value) {
            if ($value === 0) {
                unset($results['hits']['hits'][0]['_source']['data'][$key]);
            }
        }

        return new JsonResponse($results['hits']['hits'][0]['_source']['data']);
    }

    /**
     * @param string $requestContent
     * @return array
     */
    protected function parseRequestContent(string $requestContent): array
    {
        $searchParams = [];

        foreach (explode('&', $requestContent) as $chunk) {
            $param = explode("=", $chunk);

            $searchParams[$param[0]] = $param[1];
        }

        return $searchParams;
    }
}
