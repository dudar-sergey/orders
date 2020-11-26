<?php


namespace App\ebay;


use Symfony\Component\HttpClient\HttpClient;

class Allegro
{
    private $clientId;
    private $clientSecret;
    private $client;
    private $accessToken;
    private $tokenForUser;

    public function __construct()
    {
        $this->clientId = 'a95c7c8a0337479daa2813be1f8cb669';
        $this->clientSecret = 'KcHtgbkCK9XdQhUu0vYvpVGzNuFdfCctAbUt2ugstAkgvacYzai8PhT9H9iyUyHV';
        $this->client = HttpClient::create([
            'proxy'=>'http://Aw62UQ:7E0Jb2@45.134.55.94:8000',
        ]);
        $this->createAccessToken();
    }

    public function getTokenBase64()
    {
        return base64_encode($this->clientId.':'.$this->clientSecret);
    }


    public function createAccessToken()
    {
        $response = $this->client->request('POST', 'https://allegro.pl/auth/oauth/token?grant_type=client_credentials', [
            'headers' => [
                'Authorization' => 'Basic '.$this->getTokenBase64(),
            ],
        ]);
        $this->accessToken = json_decode($response->getContent(), true)['access_token'];
    }

    public function getTokenForUser($code)
    {
        $url = 'https://allegro.pl/auth/oauth/token?grant_type=authorization_code&code='.$code.'&redirect_uri=https://localhost:8000/allAuth';
        $response = $this->client->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Basic '.$this->getTokenBase64(),
            ],
        ]);
        $accessToken = json_decode($response->getContent(), true);
        return $accessToken['access_token'];
    }

    public function getCategoriesFromAllegro()
    {
        try {
            return $this->client->request('GET', 'https://api.allegro.pl/sale/categories', [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->accessToken,
                    'Accept' => 'application/vnd.allegro.public.v1+json',
                ],
            ]);
        }catch (\Exception $e) {
            var_dump($e);
        }
        return null;
    }

    public function getCategoryById($id)
    {
        try {
            return $this->client->request('GET', 'https://api.allegro.pl/sale/categories/'.$id, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->accessToken,
                    'Accept' => 'application/vnd.allegro.public.v1+json',
                ],
            ]);
        }catch (\Exception $e) {
            var_dump($e);
        }
        return null;
    }

    public function getParametersByCategoryId($categoryId)
    {
        try {
            return $this->client->request('GET', 'https://api.allegro.pl/sale/categories/'.$categoryId.'/parameters', [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->accessToken,
                    'Accept' => 'application/vnd.allegro.public.v1+json',
                ],
            ]);
        }catch (\Exception $e) {
            var_dump($e);
        }
        return null;
    }
}