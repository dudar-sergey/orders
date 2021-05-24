<?php


namespace App\ebay;


use App\Entity\AllegroOffer;
use App\Entity\Images;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Profile;
use App\Entity\Sale;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class AllegroUserManager
{
    private $session;
    private $client;
    private $em;

    public function __construct(SessionInterface $session, EntityManagerInterface $em)
    {
        $this->session = $session;
        $this->client = HttpClient::create([
            //'proxy'=>'http://wNogF3:k1VdVC@185.183.161.196:8000',
        ]);
        $this->em = $em;
    }

    public function getTokenBase64($clientId, $clientSecret)
    {
        return base64_encode($clientId . ':' . $clientSecret);
    }

    public function getTokenForUser($code, $clientId, $clientSecret)
    {
        $url = 'https://allegro.pl/auth/oauth/token?grant_type=authorization_code&code=' . $code . '&redirect_uri=https://api288gg987124.greenauto.site/allAuth';
        $response = $this->client->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Basic ' . $this->getTokenBase64($clientId, $clientSecret),
            ],
        ]);
        $accessToken = json_decode($response->getContent(), true);
        return [
            'accessToken' => $accessToken['access_token'],
            'refreshToken' => $accessToken['refresh_token']
        ];
    }

    public function getTokenForUserWithRefreshToken(Profile $profile): array
    {
        $refreshToken = $profile->getAllegroRefreshToken();
        $url = 'https://allegro.pl/auth/oauth/token?grant_type=refresh_token&refresh_token=' . $refreshToken . '&redirect_uri=http://localhost:8000/allAuth';
        $response = $this->client->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Basic ' . $this->getTokenBase64($profile->getClientId(), $profile->getClientSecret()),
            ],
        ]);
        $accessToken = json_decode($response->getContent(), true);

        return [
            'accessToken' => $accessToken['access_token'],
            'refreshToken' => $accessToken['refresh_token'],
        ];
    }

    public function getDeliverySettings()
    {
        $response = $this->client->request('GET', 'https://api.allegro.pl/sale/shipping-rates', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->session->get('currentProfile')->getAllegroAccessToken(),
                'Accept' => 'application/vnd.allegro.public.v1+json',
                'Content-Type' => 'application/vnd.allegro.public.v1+json'
            ],
        ]);
        return new JsonResponse($response->getContent(), 200, [], true);
    }

    public function getImplWar()
    {
        $response = $this->client->request('GET', 'https://api.allegro.pl/after-sales-service-conditions/implied-warranties', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->session->get('currentProfile')->getAllegroAccessToken(),
                'Accept' => 'application/vnd.allegro.public.v1+json',
                'Content-Type' => 'application/vnd.allegro.public.v1+json'
            ],
        ]);
        return new JsonResponse($response->getContent(), 200, [], true);
    }

    public function getParameters($categoryId, Profile $profile)
    {
        $response = $this->client->request('GET', 'https://api.allegro.pl/sale/categories/' . $categoryId . '/parameters', [
            'headers' => [
                'Authorization' => 'Bearer ' . $profile->getAllegroAccessToken(),
                'Accept' => 'application/vnd.allegro.public.v1+json',
                'Content-Type' => 'application/vnd.allegro.public.v1+json'
            ],
        ]);

        return new JsonResponse($response->getContent(), 200, [], true);
    }

    public function getReturnSettings()
    {
        $response = $this->client->request('GET', 'https://api.allegro.pl/after-sales-service-conditions/return-policies', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->session->get('currentProfile')->getAllegroAccessToken(),
                'Accept' => 'application/vnd.allegro.public.v1+json',
                'Content-Type' => 'application/vnd.allegro.public.v1+json'
            ],
        ]);
        return new JsonResponse($response->getContent(), 200, [], true);
    }


    /**
     * @param array $offers
     * @param string $command - команда для публикации или снятия публикации ('ACTIVATE' - выложить, 'END' - снять с публикации)
     * @param Profile $profile
     * @return JsonResponse
     * Возвращает JsonResponse
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function changeStatusOffer(array $offers, string $command, Profile $profile)
    {
        $offersEntities = [];
        foreach ($offers as $offer) {
            $offersEntities[] = $this->em->getRepository(AllegroOffer::class)->findOneBy(['allegroId' => $offer['id']]);
        }
        if ($offers) {
            $requestBody = [
                'offerCriteria' => [
                    [
                        'offers' => $offers,
                        'type' => 'CONTAINS_OFFERS'
                    ],
                ],
                'publication' => [
                    'action' => $command,
                ],
            ];
            $requestBody = json_encode($requestBody, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
            $response = $this->client->request('PUT', 'https://api.allegro.pl/sale/offer-publication-commands/' . $this->GUIDv4(), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $profile->getAllegroAccessToken(),
                    'Accept' => 'application/vnd.allegro.public.v1+json',
                    'Content-Type' => 'application/vnd.allegro.public.v1+json'
                ],
                'body' => $requestBody
            ]);

            if ($command == 'ACTIVATE') {
                foreach ($offersEntities as $offer) {
                    $offer->setStatus(true);
                }
            } elseif ($command === 'END') {
                foreach ($offersEntities as $offer) {
                    $offer->setStatus(false);
                }
            }
            $this->em->flush();

            return new JsonResponse($response->getContent(), 200, [], true);
        } else {
            return new JsonResponse(['error' => 'Оффер не найден'], 400);
        }
    }

    public function getOrdersFromAllegro(Profile $profile = null)
    {
        if ($profile) {
            $token = $profile->getAllegroAccessToken();
        } else {
            $token = $this->session->get('currentProfile')->getAllegroAccessToken();
        }
        $response = $this->client->request('GET', 'https://api.allegro.pl/order/checkout-forms', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/vnd.allegro.public.v1+json',
            ],
        ]);
        return $response->getContent();
    }


    public function addOfferToAllegro(Product $product, Profile $profile, bool $kit = null)
    {
        $name = $product->getAllegroTitle();
        if ($product->getDes() == null) {
            var_dump('Нет описания ' . $product->getId());
            return 0;
        }
        $kit == true ? $description = $product->getDescription() : $description = str_ireplace('{auto}', $product->getAuto(), $product->getDes()->getPlDes());
        $categoryId = $product->getDes()->getAllegroCategoryId();
        $upc = $product->getUpc();
        $images = $this->em->getRepository(Images::class)->findImages($product, $profile);
        $arrImage = [];
        foreach ($images as $image) {
            $arrImage[] = ['url' => $image->getUrl()];
        }

        $requestBody = [
            'name' => $name,
            'category' => [
                'id' => $product->getDes()->getAllegroCategoryId(),
            ],
            'images' =>
                $arrImage,

            'parameters' => $this->getParametersForProduct($product),
            'description' => [
                'sections' => [
                    [
                        'items' => [
                            [
                                'type' => 'TEXT',
                                'content' => $description,
                            ],
                        ],
                    ],
                ]
            ],
            'afterSalesServices' => [
                'impliedWarranty' => [
                    'id' => $profile->getAfterSaleService()->getImpliedWarranty(),
                ],
                'returnPolicy' => [
                    'id' => $profile->getAfterSaleService()->getReturnPolicy(),
                ],
            ],
            'location' => [
                'city' => 'Warszawa',
                'countryCode' => 'PL',
                'postCode' => '03-729',
                'province' => 'MAZOWIECKIE',
            ],
            'external' => [
                'id' => $product->getArticul(),
            ],
            'sellingMode' => [
                'format' => 'BUY_NOW',
                'price' => [
                    'amount' => $product->getPrice(),
                    'currency' => 'PLN',
                ],
            ],
            'delivery' => [
                'shippingRates' => [
                    'id' => $product->getDeliveryMethod($profile)->getMethodId()
                ],
                'handlingTime' => 'PT0S',
            ],
            'payments' => [
                'invoice' => 'VAT'
            ],
            'stock' => [
                'available' => $product->getQuantity(),
                'unit' => 'UNIT'
            ],
        ];
        $requestBody = json_encode($requestBody, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP);
        try {
            $response = $this->client->request('POST', 'https://api.allegro.pl/sale/offers', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $profile->getAllegroAccessToken(),
                    'Content-Type' => 'application/vnd.allegro.public.v1+json',
                    'Accept' => 'application/vnd.allegro.public.v1+json',
                ],
                'body' => $requestBody,
            ]);
            return json_decode($response->getContent(), true);
        } catch (\Exception $e) {
            var_dump($requestBody);
            var_dump([
                'headers' => [
                    'Authorization' => 'Bearer ' . $profile->getAllegroAccessToken(),
                    'Content-Type' => 'application/vnd.allegro.public.v1+json',
                    'Accept' => 'application/vnd.allegro.public.v1+json',
                ],
                'body' => $requestBody,
            ]);
            return 'Не удалось добавить товар номер ' . $product->getId();
        }
    }

    public function getOfferFromAllegro($offerId, Profile $profile, $json = false)
    {
        $response = null;
        $content = null;
        try {
            $response = $this->client->request('GET', 'https://api.allegro.pl/sale/offers/' . $offerId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $profile->getAllegroAccessToken(),
                    'Accept' => 'application/vnd.allegro.public.v1+json',
                ],
            ]);
        } catch (\Exception $e) {
            var_dump('Ошибка получения оффера');
        }

        if ($response) {
            try {
                $content = $response->getContent();
            } catch (\Exception $e) {
                var_dump($e->getMessage());
            }

        }

        if ($json) {
            return $content;
        } else {
            return json_decode($content, true);
        }
    }

    function GUIDv4($trim = true)
    {
        // Windows
        if (function_exists('com_create_guid') === true) {
            if ($trim === true)
                return trim(com_create_guid(), '{}');
            else
                return com_create_guid();
        }

        // OSX/Linux
        if (function_exists('openssl_random_pseudo_bytes') === true) {
            $data = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }

        // Fallback (PHP 4.2+)
        mt_srand((double)microtime() * 10000);
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);                  // "-"
        $lbrace = $trim ? "" : chr(123);    // "{"
        $rbrace = $trim ? "" : chr(125);    // "}"
        $guidv4 = $lbrace .
            substr($charid, 0, 8) . $hyphen .
            substr($charid, 8, 4) . $hyphen .
            substr($charid, 12, 4) . $hyphen .
            substr($charid, 16, 4) . $hyphen .
            substr($charid, 20, 12) .
            $rbrace;
        return $guidv4;
    }

    public function getOffersFromAllegro($profile)
    {
        $response = null;
        try {
            $response = $this->client->request('GET', 'https://api.allegro.pl/sale/offers?limit=1000', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $profile->getAllegroAccessToken(),
                    'Accept' => 'application/vnd.allegro.public.v1+json',
                ],
            ])->getContent();
            $response = json_decode($response, true);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
        return $response;
    }

    public function getAnOrder($orderId)
    {
        $response = null;
        try {
            $response = $this->client->request('GET', 'https://api.allegro.pl/order/checkout-forms/' . $orderId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->session->get('currentProfile')->getAllegroAccessToken(),
                    'Accept' => 'application/vnd.allegro.public.v1+json',
                ],
            ])->getContent();
        } catch (\Exception $e) {
        }

        return $response;
    }

    public function changeQuantity(AllegroOffer $allegroOffer, $quantity)
    {
        $requestBody = [
            'stock' => [
                'available' => $quantity,
                'unit' => 'UNIT',
            ],
        ];
        $response = '';
        $requestBody = json_encode($requestBody, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        try {
            $response = $this->client->request('PATCH', 'https://api.allegro.pl/sale/product-offers/' . $allegroOffer->getAllegroId(), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $allegroOffer->getProfile()->getAllegroAccessToken(),
                    'Accept' => 'application/vnd.allegro.beta.v2+json',
                    'Content-Type' => 'application/vnd.allegro.beta.v2+json'
                ],
                'body' => $requestBody
            ])->getContent();
        } catch (\Exception $e) {
            return 'Товар '.$allegroOffer->getProduct()->getArticul().' не удалось изменить';
        }
        return 'Товар '.$allegroOffer->getProduct()->getArticul().' изменен';
    }

    public function getParametersForProduct(Product $product) {
        $parameters = [
            [
                'id' => '11323',
                'valuesIds' => [
                    '11323_1'
                ],
            ],
            [
                'id' => '225693',
                'values' => [
                    $product->getUpc(),
                ]
            ],
            [
                'id' => '234493',
                'values' => [
                    $product->getArticul()
                ]
            ],
        ];
        if($product->getCategory()->getId() == 1) {
            $parameters[] = [
                'id' => '211006',
                'valuesIds' => [
                    '211006_794157'
                ],
            ];
            $parameters[] = [
                'id' => '23348',
                'valuesIds' => [
                    '23348_4'
                ],
            ];
        } elseif ($product->getCategory()->getId() == 2) {
            $parameters[] = [
                'id' => '237194',
                'values' => [
                    'A-Technic Factory'
                ]
            ];
        }

        return $parameters;
    }
}