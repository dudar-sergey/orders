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
                $offer = new AllegroOffer();
                $offer
                    ->setProduct($product)
                    ->setProfile($profile)
                    ->setAllegroId($allegroOffer['id']);
                if($allegroOffer['publication']['status'] == 'ACTIVE') {
                    $offer->setStatus(1);
                }
                $this->em->persist($offer);
            }
        }
        $this->em->flush();
    }

    protected function remove($entities)
    {
        foreach ($entities as $entity) {
            $this->em->remove($entity);
        }
        $this->em->flush();
    }
}