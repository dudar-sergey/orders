<?php


namespace App\Add;


use App\ebay\AllegroUserManager;
use App\Entity\AllegroOffer;
use App\Entity\Product;
use App\Entity\Profile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ProductPlacementManager extends Manager
{
    public function __construct(EntityManagerInterface $em, AllegroUserManager $am)
    {
        parent::__construct($em, $am);
    }

    public function changeQuantityProduct(Product $product, $quantity, $sourceProfile = null): array
    {
        $response = [];
        /** @var AllegroOffer[] $allegroOffers */
        $allegroOffers = $product->getAllegroOffers();
        foreach ($allegroOffers as $allegroOffer) {
            if ($sourceProfile) {
                if ($allegroOffer->getProfile()->getId() != $sourceProfile->getId()) {
                    $this->am->changeQuantity($allegroOffer, $quantity);
                    $this->log('Изменило количество на складе и на площадках товар '.$allegroOffer->getProduct()->getArticul().' Количество '.$quantity);
                    if($quantity < 0 || $quantity == 0) {
                        try {
                            $response = $this->am->changeStatusOffer([$allegroOffer->getAllegroId()], 'END', $allegroOffer->getProfile());
                        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
                            $this->log('Ошибка при изменении количества');
                        }
                        $this->log('Товар '.$product->getArticul().' закончился. Ответ: '.$response);
                    }
                }
            } else {
                $this->am->changeQuantity($allegroOffer, $quantity);
                if($quantity < 0 || $quantity == 0) {
                    try {
                        $response = $this->am->changeStatusOffer([$allegroOffer->getAllegroId()], 'END', $allegroOffer->getProfile());
                    } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
                        $this->log('Ошибка при изменении количества');
                    }
                    $this->log('Товар '.$product->getArticul().' закончился. Ответ: '.$response);
                }
            }
            $product->setQuantity($quantity);
            $this->em->flush();
        }
        return $response;
    }

    public function syncQuantityAllegro(Profile $profile, $processId)
    {
        /** @var AllegroOffer $allegroOffers */
        $allegroOffers = $this->em->getRepository(AllegroOffer::class)->getOffers($profile);
        foreach ($allegroOffers as $key => $allegroOffer) {
            if($allegroOffer->getProduct()->getQuantity() == 0) {
                $this->am->changeStatusOffer([
                   ['id' => $allegroOffer->getAllegroId()]
                ], 'END', $profile);
                $message = $this->am->changeQuantity($allegroOffer, 1);
            } else {
                $message = $this->am->changeQuantity($allegroOffer, $allegroOffer->getProduct()->getQuantity());
            }
            $this->progressRep->updateProgress($processId, $key / count($allegroOffers) * 100, $message);
        }
        $this->progressRep->updateProgress($processId, 100, 'OK');
    }
}