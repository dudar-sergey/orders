<?php


namespace App\Add;


use App\Entity\Images;
use App\Entity\Profile;

class ImageManager extends Manager
{
    public function createNewImagesForProfile($url, Profile $profile)
    {
        $products = $this->productRep->findAll();
        foreach ($products as $product) {
            $tempUrl = $url.$product->getArticul().'/'.$product->getArticul().'-MAIN.jpg';
            $urlImage = $this->send($tempUrl);
            if($urlImage) {
                $this->createImage($tempUrl, $profile, $product, true);
            }
            $tempUrl = $url.$product->getArticul().'/'.$product->getArticul().'-PT01.jpg';
            $urlImage = $this->send($tempUrl);
            if($urlImage) {
                $this->createImage($tempUrl, $profile, $product, false);
            }
            $tempUrl = $url.$product->getArticul().'/'.$product->getArticul().'-PT02.jpg';
            $urlImage = $this->send($tempUrl);
            if($urlImage) {
                $this->createImage($tempUrl, $profile, $product, false);
            }
        }
    }

    public function createImage($url, $profile, $product, $main = false)
    {
        $image = new Images();
        $image->setUrl($url)->setProfile($profile)->setMain($main)->setProduct($product);
        $this->em->persist($image);
        $this->em->flush();
    }
}