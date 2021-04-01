<?php


namespace App\Add;


use App\ebay\AllegroUserManager;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Progress;
use Doctrine\ORM\EntityManagerInterface;

class Manager
{
    protected $orderRep;
    protected $productRep;
    protected $em;
    protected $am;
    protected $progressRep;

    public function __construct(EntityManagerInterface $em, AllegroUserManager $am)
    {
        $this->em = $em;
        $this->am = $am;
        $this->orderRep = $em->getRepository(Order::class);
        $this->productRep = $em->getRepository(Product::class);
        $this->progressRep = $em->getRepository(Progress::class);
    }
}