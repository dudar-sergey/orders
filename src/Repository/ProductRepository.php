<?php

namespace App\Repository;

use App\Entity\AllegroOffer;
use App\Entity\Images;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\All;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Product::class);
        $this->em = $em;
    }

    /**
     * @param $value
     * @return Product[] Returns an array of Product objects
     */

    public function findByArticleAndName(string $value): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.articul LIKE :search')
            ->orWhere('o.name LIKE :search')
            ->orWhere('o.upc LIKE :search')
            ->setParameter('search', '%'.$value.'%')
            ->getQuery()
            ->getResult()
        ;
    }

    public function createProduct($data)
    {

        $article = $data['article'];
        $img = $data['img'];
        $name = $data['name'];
        $price = $data['price'];
        $quantity = $data['quantity'];
        $allegroOffer = $data['allegroOffer'];

        $product = $this->findOneBy(['articul' => $data['article']]);
        if(!$product) {
            $product = new Product();
            $image = new Images();
            $image->setUrl($data['img']);
            $this->em->persist($image);
            $product->addImage($image);
        }

        $newAllegroOffer = $this->em->getRepository(AllegroOffer::class)->findOneBy(['allegroId' => $allegroOffer]);
        if(!$newAllegroOffer)
        {
            $newAllegroOffer = new AllegroOffer();
            $newAllegroOffer->setAllegroId($allegroOffer);
            $newAllegroOffer->setStatus('ACTIVE');
            $this->em->persist($newAllegroOffer);
            $product->setAllegroOffer($newAllegroOffer);
        }

        $product->setName($name);
        $product->setPrice($price);
        $product->setQuantity($quantity);
        $product->setArticul($article);
        $this->em->persist($product);
        $this->em->flush();
    }
}
