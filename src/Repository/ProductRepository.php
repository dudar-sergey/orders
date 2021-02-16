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
     * @param null $name
     * @param null $article
     * @param null $limit
     * @param null $offset
     * @return Product[] Returns an array of Product objects
     */

    public function findByArticleAndName($name = null, $article = null, $limit = null,  $offset = null): array
    {
        if(!$name && !$article) {
            return $this->findBy([],[], $limit, $offset);
        }
        $query = $this->createQueryBuilder('o');
        if($article)
        {
            $query->where('o.articul LIKE :article')->setParameter('article', '%'.$article.'%')
            ;
        }
        if($name)
        $query->orWhere('o.name LIKE :name')->setParameter('name', '%'.$name.'%')
        ;

        if($limit)
        {
            $query
                ->setMaxResults($limit);
        }
        if($offset)
            $query->setFirstResult($offset);
        return $query->getQuery()->getResult();
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
            $product->setArticul($article);
            $product->setName($name);
            $product->setPrice($price);
        }

        $newAllegroOffer = $this->em->getRepository(AllegroOffer::class)->findOneBy(['allegroId' => $allegroOffer]);
        if(!$newAllegroOffer)
        {
            $newAllegroOffer = new AllegroOffer();
            $newAllegroOffer->setAllegroId($allegroOffer);
            $newAllegroOffer->setStatus('1');
            $this->em->persist($newAllegroOffer);
            $product->setAllegroOffer($newAllegroOffer);
        }
        $product->setQuantity($quantity);
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
            if($quantity)
            {
                $product->addQuantity($quantity);
            }
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
