<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;


class Medecins
{
    public function getMedecins(string $param)
    {
        $result = null;
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://public.opendatasoft.com/api/records/1.0/search/?dataset=medecins&q='.$param);

        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = $response->getContent();
        $json = json_decode($content);

        foreach($json->records as $medecins){
            if($medecins->fields->nom == $param){
                $result = $medecins->fields->nom;
            break;
            }
        }

        return $result;
    }
    public function getSearch(string $param)
    {
        $result = null;
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://public.opendatasoft.com/api/records/1.0/search/?dataset=annuaire-des-professionnels-de-sante&q='.$param.'&rows=21');

        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $json = json_decode($content);


        return $json->records;
    }
}