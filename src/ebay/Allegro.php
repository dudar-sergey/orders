<?php


namespace App\ebay;


use App\Controller\user\UserController;
use Symfony\Component\HttpClient\HttpClient;

class Allegro
{
    private $clientId;
    private $clientSecret;
    private $client;
    private $accessToken;

    public function __construct($clientId, $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->client = HttpClient::create([
            //'proxy'=>'http://wNogF3:k1VdVC@185.183.161.196:8000',
        ]);
        $this->createAccessToken();
    }


    public function createAccessToken()
    {
        try{
            $response = $this->client->request('POST', 'https://allegro.pl/auth/oauth/token?grant_type=client_credentials', [
                'headers' => [
                    'Authorization' => 'Basic '.$this->getTokenBase64(),
                ],
            ]);
            $this->accessToken = json_decode($response->getContent(), true)['access_token'];
        }catch (\Exception $e){}
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