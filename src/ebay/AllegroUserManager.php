<?php


namespace App\ebay;


use App\Entity\AllegroOffer;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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

    public function getDeliverySettings()
    {
        $response = $this->client->request('GET', 'https://api.allegro.pl/sale/offers/9939962050', [
            'headers' => [
                'Authorization' => 'Bearer '.$this->session->get('userTokenAllegro'),
                'Accept' => 'application/vnd.allegro.public.v1+json',
                'Content-Type' => 'application/vnd.allegro.public.v1+json'
            ],
        ]);
        return $response;
    }


    /**
     * @param integer $id - id предложения Allegro
     * @param string $command - команда для публикации или снятия публикации ('ACTIVATE' - выложить, 'END' - снять с публикации)
     * @return JsonResponse
     * Возвращает JsonResponse
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function changeStatusOffer(int $id, string $command)
    {
        $offer = $this->em->getRepository(AllegroOffer::class)->findOneBy(['allegroId' => $id]);
        if($offer)
        {
            $requestBody = [
                'offerCriteria' => [
                    [
                        'offers' => [
                            [
                                'id' => $id,
                            ],
                        ],
                        'type' => 'CONTAINS_OFFERS'
                    ],
                ],
                'publication' => [
                    'action' => $command,
                ],
            ];
            $requestBody = json_encode($requestBody, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
            $response = $this->client->request('PUT', 'https://api.allegro.pl/sale/offer-publication-commands/'.$this->GUIDv4(), [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->session->get('userTokenAllegro'),
                    'Accept' => 'application/vnd.allegro.public.v1+json',
                    'Content-Type' => 'application/vnd.allegro.public.v1+json'
                ],
                'body' => $requestBody
            ]);

            $offer->setStatus($command);
            $this->em->persist($offer);
            $this->em->flush();

            return new JsonResponse($response->getContent(), 200, [], true);
        }
        else
        {
            return new JsonResponse(['error' => 'Оффер не найден'], 400);
        }
    }

    public function getOrdersFromAllegro()
    {
        $response = $this->client->request('GET', 'https://api.allegro.pl/order/checkout-forms', [
            'headers' => [
                'Authorization' => 'Bearer '.$this->session->get('userTokenAllegro'),
                'Accept' => 'application/vnd.allegro.public.v1+json',
            ],
        ]);
        return $response->getContent();
    }


    public function addOfferToAllegro(Product $product): JsonResponse
    {
        $name = $product->getAllegroTitle();
        $description = str_ireplace('{auto}', $product->getAuto(), $product->getDes()->getPlDes());
        $categoryId = '257947';
        $upc = $product->getUpc();
        $images = $product->getImages();
        $arrImage = [];
        foreach ($images as $image)
        {
            $arrImage[] = ['url' => $image->getUrl()];
        }

        $requestBody = [
          'name' => $name,
          'category' => [
              'id' => $categoryId,
          ],
          'images' =>
              $arrImage,

          'parameters' => [
              [
                'id' => '11323',
                'valuesIds' => [
                  '11323_1'
                ],
              ],
              [
                'id' => '225693',
                'values' => [
                    $upc,
                ]
              ],
          ],
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
              'id' => 'bd9ae975-aece-40d5-a81e-00e8dd502425'
            ],
            'returnPolicy' => [
              'id' => '3d9208d3-3ee3-4a76-8e48-5b0fc5c0173e',
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
              'id' => '3d68ea9f-8e59-4517-9102-6984ba39bbb0'
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
        $requestBody = json_encode($requestBody, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        $response = $this->client->request('POST', 'https://api.allegro.pl/sale/offers', [
            'headers' => [
                'Authorization' => 'Bearer '.$this->session->get('userTokenAllegro'),
                'Content-Type' => 'application/vnd.allegro.public.v1+json',
                'Accept' => 'application/vnd.allegro.public.v1+json',
            ],
            'body' => $requestBody,
        ]);
        return new JsonResponse($response->getContent(), 200, [], true);
    }

    public function getOfferFromAllegro($offerId, $json = false)
    {
        $response = null;
        $content = null;
        try {
            $response = $this->client->request('GET', 'https://api.allegro.pl/sale/offers/'.$offerId, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->session->get('userTokenAllegro'),
                    'Accept' => 'application/vnd.allegro.public.v1+json',
                ],
            ]);
        }
        catch (\Exception $e)
        {
            var_dump('Ошибка получения оффера');
        }

        if($response)
        {
            try
            {
                $content = $response->getContent();
            }
            catch (\Exception $e)
            {
                var_dump($e->getMessage());
            }

        }

        if($json)
        {
            return $content;
        }
        else
        {
            return json_decode($content, true);
        }
    }

    /*public function putOfferAllegro($allegroOfferId)
    {
        $jsonContent = $this->getOfferFromAllegro($allegroOfferId);

        $response = $this->client->request('PUT', 'https://api.allegro.pl/sale/offers/'.$allegroOfferId, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->session->get('userTokenAllegro'),
                'Content-Type' => 'application/vnd.allegro.public.v1+json',
                'Accept' => 'application/vnd.allegro.public.v1+json',
            ],
            'body' => $jsonContent,
        ]);
        return new JsonResponse($response->getContent(), 200, [], true);
    }*/

    function GUIDv4 ($trim = true)
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
        $guidv4 = $lbrace.
            substr($charid,  0,  8).$hyphen.
            substr($charid,  8,  4).$hyphen.
            substr($charid, 12,  4).$hyphen.
            substr($charid, 16,  4).$hyphen.
            substr($charid, 20, 12).
            $rbrace;
        return $guidv4;
    }

    public function getOffersFromAllegro()
    {
        $response = null;
        try {
            $response = $this->client->request('GET', 'https://api.allegro.pl/sale/offers?limit=1000&publication.status=ACTIVE', [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->session->get('userTokenAllegro'),
                    'Accept' => 'application/vnd.allegro.public.v1+json',
                ],
            ])->getContent();
            $productRep = $this->em->getRepository(Product::class);
            $response = json_decode($response, true);
            foreach ($response['offers'] as $offer)
            {
                $productRep->createProduct([
                    'name' => $offer['name'],
                    'article'  =>$offer['external']['id'],
                    'img' => $offer['primaryImage']['url'],
                    'price'  => $offer['sellingMode']['price']['amount'],
                    'quantity' => $offer['stock']['available'],
                    'allegroOffer' => $offer['id'],
                ]);
            }
        }
        catch (\Exception $e)
        {
            var_dump($e->getMessage());
        }
    }

    public function syncOrdersFromAllegro()
    {
        $response = json_decode($this->getOrdersFromAllegro(), true);
        $orders = $response['checkoutForms'] ?? null;

        if($orders){
            foreach ($orders as $order)
            {
                $offerId = $order['lineItems'][0]['offer']['id'];
                $this->em->getRepository(Order::class)->createUpdateOrder([
                    'allegroOfferId' => $offerId,
                    'price'  => $order['summary']['totalToPay']['amount'],
                    'buyer' => $order['buyer']['firstName'].' '.$order['buyer']['lastName'].' '.$order['buyer']['login'],
                    'payment' => $order['status'],
                    'date' => new \DateTime($order['updatedAt']),
                    'placement' => 'allegro',
                    'allegroId' => $order['id']
                ]);
            }
        }
    }

    public function getAnOrder($orderId)
    {
        $response = null;
        try {
            $response = $this->client->request('GET', 'https://api.allegro.pl/order/checkout-forms/'.$orderId, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->session->get('userTokenAllegro'),
                    'Accept' => 'application/vnd.allegro.public.v1+json',
                ],
            ])->getContent();
        }
        catch (\Exception $e)
        {
            var_dump($e->getMessage());
        }

        return $response;
    }
}