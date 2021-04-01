<?php


namespace App\Add;


use App\ebay\AllegroUserManager;
use App\Entity\AllegroOffer;
use App\Entity\Product;
use App\Entity\Profile;
use Doctrine\ORM\EntityManagerInterface;

class ProductPlacementManager extends Manager
{
    public function __construct(EntityManagerInterface $em, AllegroUserManager $am)
    {
        parent::__construct($em, $am);
    }

    public function changeQuantityProduct(Product $product, $quantity, $sourceProfile = null): array
    {
        $response = [];
        foreach ($product->getAllegroOffers() as $allegroOffer) {
            if ($sourceProfile) {
                if ($allegroOffer->getProfile()->getId() != $sourceProfile->getId()) {
                    $this->am->changeQuantity($allegroOffer, $quantity);
                }
            } else {
                $this->am->changeQuantity($allegroOffer, $quantity);
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