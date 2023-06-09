<?php


namespace App\ebay;


use App\Entity\AllegroOffer;
use App\Entity\Product;
use App\Entity\Profile;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AllegroManager extends AllegroUserManager
{
    public function getOfferById(string $id, Profile $profile)
    {
        try {
            return $this->getOfferFromAllegro($id, $profile);
        } catch (Exception $e) {
            return false;
        }
    }

    public function syncAllegroProducts(Profile $profile)
    {
        $allegroOffers = $this->getOffersFromAllegro($profile);
        $profile = $this->em->find(Profile::class, $profile->getId());
        if ($allegroOffers) {
            $this->remove($profile->getAllegroOffers());
            foreach ($allegroOffers['offers'] as $allegroOffer) {
                $product = $this->em->getRepository(Product::class)->findOneBy(['articul' => $allegroOffer['external']['id']]);
                $offer = $this->em->getRepository(AllegroOffer::class)->findOneBy(['allegroId' => $allegroOffer['id']]);
                if (!$offer) {
                    $offer = new AllegroOffer();
                    $offer
                        ->setProduct($product)
                        ->setProfile($profile)
                        ->setAllegroId($allegroOffer['id']);

                    $this->em->persist($offer);
                }
                if ($allegroOffer['publication']['status'] == 'ACTIVE') {
                    $offer->setStatus(1);
                } else {
                    $offer->setStatus(0);
                }
            }
        }
        $this->em->flush();
    }

    protected function remove($entities)
    {
        foreach ($entities as $entity) {
            $entity->setStatus(null);
        }
        $this->em->flush();
    }

    public function proposeProduct($profile, Product $product)
    {
        $description = str_ireplace('{auto}', $product->getAuto(), $product->getDes()->getPlDes());
        $images = [];
        foreach ($product->getImages() as $image) {
            $images[] = [
                'url' => $image->getUrl(),
            ];
        }
        $requestBody = [
            'name' => $product->getAllegroTitle(),
            'category' => [
                'id' => $product->getDes()->getAllegroCategoryId(),
            ],
            'parameters' => $this->getParametersForProduct($product),
            'images' => $product->getAllegroImages(),
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

        ];
        $requestBody = json_encode($requestBody, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP);
        $response = $this->client->request('POST', 'https://api.allegro.pl/sale/product-proposals', [
            'headers' => [
                'Authorization' => 'Bearer ' . $profile->getAllegroAccessToken(),
                'Content-Type' => 'application/vnd.allegro.public.v1+json',
                'Accept' => 'application/vnd.allegro.public.v1+json',
            ],
            'body' => $requestBody,
        ]);

        return json_decode($response->getContent(), true);
    }

    public function uploadImage(Profile $profile, string $url)
    {
        $requestBody = [
            'url' => $url,
        ];
        $requestBody = json_encode($requestBody, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP);
        $response = $this->client->request('POST', 'https://upload.allegro.pl/sale/images', [
            'headers' => [
                'Authorization' => 'Bearer ' . $profile->getAllegroAccessToken(),
                'Content-Type' => 'application/vnd.allegro.public.v1+json',
                'Accept' => 'application/vnd.allegro.public.v1+json',
            ],
            'body' => $requestBody,
        ]);
        return json_decode($response->getContent(), true);
    }

}