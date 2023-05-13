<?php


namespace App\Add;


use App\Controller\api\ProductApi\ProductApi;
use App\ebay\AllegroUserManager;
use App\ebay\Ebay;
use App\Entity\AllegroOffer;
use App\Entity\Images;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Profile;
use App\Entity\Sale;
use App\Entity\Supply;
use App\Entity\SupplyProduct;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class Add
{
    private $em;
    private $ebay;
    private $serializer;
    private $am;
    private $session;

    public function __construct(EntityManagerInterface $em, Ebay $ebay, AllegroUserManager $am, SessionInterface $session)
    {
        $this->em = $em;
        $this->ebay = $ebay;
        $this->am = $am;
        $this->session = $session;
        $this->serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
    }

    public function addDefaultSupply($data, $files = null)
    {
        $header = $data['header'];
        unset($data['header']);
        unset($data['file']);
        $products = [];
        foreach ($data as $supplyProduct) {
            if (isset($supplyProduct['name']) && isset($supplyProduct['articul'])) {

                $products[] = $this->em->getRepository(Product::class)->addProductFromSupply($supplyProduct);
            } else {
                return 0;
            }
        }
        if ($files) {
            foreach ($files as $file) {
                $supplyFromCsv = $this->csvToArray(file_get_contents($file->getPathname()));
            }
            foreach ($supplyFromCsv as $productFromCsv) {
                if (isset($productFromCsv['article'])) {
                    if ($productFromCsv['article']) {
                        $NProduct = $this->em->getRepository(Product::class)->addProductFromSupply($productFromCsv);
                        $products[] = ['product' => $NProduct, 'quantity' => $productFromCsv['quantity'] ?? null];
                    }
                }
            }
        }
        if (!empty($products)) {
            $supply = new Supply();
            $supply->setSender($header['sender']);
            $supply->setRecipient($header['recipient']);
            $supply->setDate(new \DateTime());
            $supply->setContract($header['contract']);
            $this->em->persist($supply);
            foreach ($products as $product) {
                $this->em->getRepository(SupplyProduct::class)->createSupplyProduct($supply, $product['product'], $product['quantity']);
            }
            $this->em->flush();
        }
        return 1;
    }

    public function deleteProducts($products)
    {
        foreach ($products as $product) {
            $this->em->remove($product);
        }
        $this->em->flush();
    }

    static function csvToArray($file, $delimiter = ';')
    {
        $context = [
            CsvEncoder::DELIMITER_KEY => $delimiter,
            CsvEncoder::ENCLOSURE_KEY => '"',
            CsvEncoder::ESCAPE_CHAR_KEY => '\\',
            CsvEncoder::KEY_SEPARATOR_KEY => ',',
        ];
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        return $serializer->decode($file, 'csv', $context);
    }

    public function syncToEbay($products)
    {
        foreach ($products as $product) {
            $data['title'] = $product->getName();
            $data['description'] = $product->getDescription();
            $data['quantity'] = $product->getQuantity();
            $data['upc'] = $product->getUpc();
            $data['price'] = $product->getPrice();
            $result = $this->ebay->addItem($data);
            if ($result['Ack'] == 'Failure') {
                foreach ($result['Errors'] as $error) {
                    var_dump($error['LongMessage']);
                }
            }
            if (isset($result['ItemID'])) {

                $product->seteId($result['ItemID']);
                $product->setSync(true);
                $this->em->persist($product);

            }
        }
    }

    public function syncOrdersFromEbay()
    {
        $response = $this->ebay->getOrdersFromEbay();
        dd($response);
        $orders = $response['OrderArray']['Order'];

        foreach ($orders as $order) {
            $inOrder = $this->em->getRepository(Order::class)->findOneBy(['eId' => $order['OrderID']]);
            $product = $this->em->getRepository(Product::class)->findOneBy(['eId' => $order['TransactionArray']['Transaction']['Item']['ItemID']]);
            if (!$inOrder) {
                $inOrder = new Order();
            }
            $date = new \DateTime($order['CreatedTime']);
            $inOrder->setDate($date);
            $inOrder->setBuyer($order['TransactionArray']['Transaction']['Buyer']['Email']);
            $inOrder->setPrice($order['TransactionArray']['Transaction']['TransactionPrice']);
            if ($product) {
                $inOrder->addProduct($product);
            }
            $inOrder->setEId($order['OrderID']);
            $inOrder->setPayment($order['CheckoutStatus']['eBayPaymentStatus']);
            $this->em->persist($inOrder);
        }
        $this->em->flush();
    }

    public function addSale($data)
    {
        $sale = new Sale();
        /** @var Product $product */
        $product = $data['product'];
        $sale->setProduct($product);
        $sale->setQuantity($data['quantity']);
        $sale->setCreateAt($data['createAt']);
        $sale->setCurrency($data['currency']);
        $sale->setPrice($data['price']);
        $sale->setPurchase($data['purchase'] ?? null);
        $sale->setOrderNumber($data['orderNumber'] ?? null);
        $sale->setStatus('sold');
        $sale->setPlatform($data['platform']);
        //$product->setQuantity($product->getQuantity() - $data['quantity']);
        $this->em->persist($sale);
        $this->em->persist($product);
        $this->em->flush();
    }

    /**
     * @param Product[] $products
     * @param $options
     */
    public function addKitProduct($products, $options)
    {
        $newProduct = new Product();
        $name = '';
        foreach ($options['images'] as $imageUrl) {
            $image = new Images();
            $image->setUrl($imageUrl);
            $this->em->persist($image);
            $newProduct->addImage($image);
        }
        if ($products) {
            foreach ($products as $product) {
                $newProduct->addKitProduct($product);
                $newProduct->setAuto($product->getAuto());
                $name .= $product->getDes()->getPlName() . '+';
            }
            $name = mb_strrchr($name, '+', true);
            $name .= ' ' . $products[0]->getAuto();
            $newProduct->setArticul($options['article']);
            $newProduct->setDescription($options['description']);
            $newProduct->setPrice($options['price']);
            $newProduct->setQuantity($this->getMinQuantityOfProducts($products));
            $newProduct->setAllegroTitle($name);
            $newProduct->setName($name);
            $newProduct->setKit(true);
            $this->em->persist($newProduct);
            $this->em->flush();
        }
    }

    protected function getMinQuantityOfProducts($products)
    {
        $min = $products[0]->getQuantity();
        foreach ($products as $product) {
            if ($product->getQuantity() < $min) {
                $min = $product->getQuantity();
            }
        }

        return $min;
    }
}