<?php

namespace App\Repository;

use App\Entity\AllegroOffer;
use App\Entity\Images;
use App\Entity\Product;
use App\Entity\ProductGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

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

    /* Если товар пришел из аллегро */
    public function createProduct($data)
    {

        $article = $data['article'] ? $data['article']: null;
        $img = $data['img'] ? $data['img']: null;
        $name = $data['name'] ? $data['name']: null;
        $price = $data['price'] ? $data['price']: null;
        $quantity = $data['quantity'] ? $data['quantity']: null;
        $allegroOffer = $data['allegroOffer'] ? $data['allegroOffer']: null;

        $product = $this->findOneBy(['articul' => $data['article']]);
        if(!$product) {
            $product = new Product();
            $image = new Images();
            $image->setUrl($img);
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

    /* Если товра пришел из поставки, в будущем я соединю эти функции, а то выглядит не очень */
    public function addProductFromSupply($data): Product
    {
        $article = isset($data['article']) ? $data['article']: null;
        $name = isset($data['name']) ? $data['name']: null;
        $quantity = isset($data['quantity']) ? $data['quantity']: null;
        $upc = isset($data['upc']) ? $data['upc']: null;
        $price = isset($data['price']) ? $data['price']: null;
        $group = isset($data['group']) ? $data['group']: null;
        $auto = isset($data['auto']) ? $data['auto']: null;
        if ($group)
        {
            $group = $this->em->getRepository(ProductGroup::class)->find($group);
        }

        if($product = $this->findOneBy(['articul' => $article]))
        {
            $quantity ?? $product->addQuantity($quantity);

        }
        else{
            $product = new Product();
            $product
                ->setName($name)
                ->setArticul($article)
                ->setQuantity($quantity)
                ->setPrice($price)
                ->setUpc($upc)
                ->setAuto($auto)
                ->setProductGroup($group)
            ;
        }
        if($article)
        {
            $this->em->persist($product);
            $this->em->flush();
        }

        return $product;
    }
}
