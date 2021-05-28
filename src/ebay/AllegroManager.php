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
        if($allegroOffers) {
            $this->remove($profile->getAllegroOffers());
            foreach ($allegroOffers['offers'] as $allegroOffer) {
                $product = $this->em->getRepository(Product::class)->findOneBy(['articul' => $allegroOffer['external']['id']]);
                $offer = $this->em->getRepository(AllegroOffer::class)->findOneBy(['allegroId' => $allegroOffer['id']]);
                if(!$offer) {
                    $offer = new AllegroOffer();
                    $offer
                        ->setProduct($product)
                        ->setProfile($profile)
                        ->setAllegroId($allegroOffer['id']);

                    $this->em->persist($offer);
                }
                if($allegroOffer['publication']['status'] == 'ACTIVE') {
                    $offer->setStatus(1);
                } else {
                    $offer->setStatus(null);
                }
            }
        }
        $this->em->flush();
    }

    protected function remove($entities)
    {
        foreach ($entities as $entity) {
            $entity->setSatus(0);
        }
        $this->em->flush();
    }
}