<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Description;
use App\Entity\Requirement;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class AbstractController
{
    private Client $client;

    public function __construct(){
        $this->client = ClientBuilder::create()->setHosts(['localhost:9200'])->build();
    }

    protected function createImage(int $idgame){
        $image = new Image();
        $imageData = json_decode($this->imagesByGame($idgame)->getContent(), true);
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

    protected function createDescription(int $idgame){
        $description = new Description();
        $descriptionData = json_decode($this->descriptionsByGame($idgame)->getContent(), true);
        if ($descriptionData['hits']['hits'] != null) {
            $description->hydrate($descriptionData['hits']['hits'][0]['_source']['data']);
            $description->setId($descriptionData['hits']['hits'][0]['_id']);
        }

        return $description;
    }

    protected function createRequirement(int $idgame){
        $requirement = new Requirement();
        $requirementData = json_decode($this->requirementsByGame($idgame)->getContent(), true);
        if ($requirementData['hits']['hits'] != null) {
            $requirement->hydrate($requirementData['hits']['hits'][0]['_source']['data']);
            $requirement->setId($requirementData['hits']['hits'][0]['_id']);
        }

        return $requirement;
    }

    protected function setSorting($sorting, $keywordArray){
        $temp = explode('-', $searchParams['sorting']);
        $criteria = $temp[0];
        $order = $temp[1];

        if(in_array($criteria, $keywordArray)){
           return array('data.' . $criteria . '.keyword:' . $order);
        }
        else{
            return array('data.' . $criteria . ':' . $order);
        }

    }

    protected function handleSpecialParams($specialParam){
        $chars = str_split($specialParam);

            $iterator = 0;
    
            foreach($chars as $key => $char)
            {
                if(ctype_upper($char) && $key !== 0){
                    array_splice($chars, $key+$iterator, 0, ' ' );
                    $iterator++;
                }
            }
            $handledParam = implode("", $chars);

            return $handledParam;
    }

    private function imagesByGame(string $appid): JsonResponse
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

    private function descriptionsByGame(string $appid): JsonResponse
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

    private function requirementsByGame(string $appid): JsonResponse
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

}