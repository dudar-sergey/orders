<?php


namespace App\Add;


use App\ebay\AllegroUserManager;
use App\Entity\DeliveryCategory;
use App\Entity\Description;
use App\Entity\Supply;
use App\Entity\SupplyProduct;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ProductManager extends Manager
{
    private $serializer;

    public function __construct(EntityManagerInterface $em, AllegroUserManager $am)
    {
        parent::__construct($em, $am);
        $this->serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
    }

    public function addSupply(UploadedFile $file, string $sender, string $recipient, DateTime $date, $contract)
    {
        $supply = $this->em->getRepository(Supply::class)->createSupply($sender, $date, $recipient, $contract);
        $products = $this->toArray(file_get_contents($file));
        $supplyProducts = [];
        foreach ($products as $product) {
            $currentProduct = $this->productRep->findOneBy(['articul' => $product['article']]);
            if ($currentProduct) {
                $this->productRep->updateProduct($currentProduct, $currentProduct->getQuantity() + $product['quantity']);
            } else {
                $category = $this->em->getRepository(DeliveryCategory::class)->find($product['category']);
                $group = $this->em->getRepository(Description::class)->find($product['group']);
                $currentProduct = $this->productRep->createProduct([
                    'article' => $product['article'],
                    'price' => $product['price'],
                    'quantity' => $product['quantity'],
                    'des' => $group,
                    'auto' => $product['auto'],
                    'category' => $category,
                    'upc' => $product['upc']
                ]);
            }
            $supplyProducts[] = $this->em->getRepository(SupplyProduct::class)->createSupplyProduct($supply, $currentProduct, $product['quantity']);
        }

        return $supplyProducts;
    }

    private function toArray($csvFile)
    {
        $context = [
            CsvEncoder::DELIMITER_KEY => ';',
            CsvEncoder::ENCLOSURE_KEY => '"',
            CsvEncoder::ESCAPE_CHAR_KEY => '\\',
            CsvEncoder::KEY_SEPARATOR_KEY => ',',
        ];
        return $this->serializer->decode($csvFile, 'csv', $context);
    }

    public function updateQuantity(UploadedFile $file)
    {
        $response = [];
        $products = $this->toArray(file_get_contents($file));
        foreach ($products as $product) {
            $currentProduct = $this->productRep->findOneBy(['articul' => $product['article']]);
            if ($currentProduct) {
                if (isset($product['quantity'])) {
                    $responseProduct = $currentProduct->setQuantity($product['quantity']);
                    $response[] = $responseProduct->getArticul();
                }
            }
        }
        $this->em->flush();
        return $response;
    }
}