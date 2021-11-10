<?php

namespace App\Repository;

use App\Entity\AllegroOffer;
use App\Entity\Description;
use App\Entity\Images;
use App\Entity\Product;
use App\Entity\ProductGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
     * @param null $sort
     * @return Product[] Returns an array of Product objects
     */

    public function findByArticleAndName($name = null, $article = null, $limit = null, $offset = null, $sort = null): array
    {
        $query = $this->createQueryBuilder('o');
        if (!$name && !$article) {
            $query->select('o');
        }
        if ($article) {
            $query->where('o.articul LIKE :article')->setParameter('article', '%' . $article . '%');
        }
        if ($name) {
            $query->orWhere('o.name LIKE :name')->setParameter('name', '%' . $name . '%');
        }
        if($sort) {
            $query->orderBy('o.'.$sort['column'], $sort['method']);
        }
        if ($limit) {
            $query
                ->setMaxResults($limit);
        }
        if ($offset)
            $query->setFirstResult($offset);
        return $query->getQuery()->getResult();
    }

    /* Если товар пришел из аллегро */
    public function createProduct($data): Product
    {
        $product = new Product();
        $article = $data['article'] ?? null;
        $name = $data['name'] ?? null;
        $price = (float)$data['price'] ?? null;
        $quantity = $data['quantity'] ?? null;
        $auto = $data['auto'] ?? null;
        /** @var Description $des */
        $des = $data['des'] ?? null;
        $category = $data['category'] ?? null;
        $upc = $data['upc'] ?? null;
        $product
            ->setQuantity($quantity)
            ->setDes($des)
            ->setCategory($category)
            ->setAllegroTitle($des->getPlName().' '.$auto)
            ->setArticul($article)
            ->setPrice($price)
            ->setName($name)
            ->setUpc($upc)
            ->setAuto($auto)
            ->setBrand('A-Technic');
        $this->em->persist($product);
        $this->em->flush();
        return $product;
    }

    /* Если товра пришел из поставки, в будущем я соединю эти функции, а то выглядит не очень */
    public function addProductFromSupply($data): Product
    {
        $article = isset($data['article']) ? $data['article'] : null;
        $name = isset($data['name']) ? $data['name'] : null;
        $quantity = isset($data['quantity']) ? $data['quantity'] : null;
        $upc = isset($data['upc']) ? $data['upc'] : null;
        $price = isset($data['price']) ? $data['price'] : null;
        $group = isset($data['group']) ? $data['group'] : null;
        $auto = isset($data['auto']) ? $data['auto'] : null;
        if ($group) {
            $group = $this->em->getRepository(Description::class)->find($group);
        }
        if ($product = $this->findOneBy(['articul' => $article])) {
            if ($quantity) {
                $product->addQuantity($quantity);
            }
        } else {
            $product = new Product();
            $product
                ->setName($name)
                ->setArticul($article)
                ->setQuantity($quantity)
                ->setPrice($price)
                ->setUpc($upc)
                ->setAuto($auto)
                ->setDes($group);
        }
        if ($article) {
            $this->em->persist($product);
            $this->em->flush();
        }

        return $product;
    }

    public function getUnloadProducts($profile): array
    {
        $unloadProducts = [];
        $products = $this
            ->createQueryBuilder('o')
            ->select('o')
            ->groupBy('')
        ;
        foreach ($this->findBy([], ['id' => 'DESC']) as $product) {
            if(!$product->getAllegroOffer($profile) && $product->getImages()[0] && $product->getDes()) {
                if($product->getQuantity() >= 1) {
                    $unloadProducts[] = $product;
                }
            }
        }

        return $unloadProducts;
    }

    public function getNonActiveProducts()
    {
        $products = [];
        $allProducts = $this->findAll();
        foreach ($allProducts as $product) {
            if($product->getAllegroOffers()) {
                $products[] = $product;
            }
        }

        return $products;
    }

    public function updateProduct(Product $product, $quantity): Product
    {
        $product->setQuantity($quantity);
        $this->em->flush();
        return $product;
    }

    public function findWithSales()
    {
        $results = [];
        foreach ($this->findAll() as $product) {
            foreach ($product->getAllegroOffers() as $allegroOffer) {
                if($allegroOffer->getOrderAllegroOffers()[0]) {
                    $results[] = $allegroOffer->getProduct();
                }
            }
        }
        return $results;
    }
}
