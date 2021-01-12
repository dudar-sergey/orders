<?php


namespace App\Add;


use App\ebay\Ebay;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Sale;
use App\Entity\Supply;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

class Add
{
    private $em;
    private $ebay;

    public function __construct(EntityManagerInterface $em, Ebay $ebay)
    {
        $this->em = $em;
        $this->ebay = $ebay;
    }

    public function addDefaultSupply($data, $files = null)
    {
        $header = $data['header'];
        unset($data['header']);
        unset($data['file']);
        $products = [];
        foreach ($data as $supply)
        {
            if(isset($supply['name']) && isset($supply['articul']))
            {
                $product = new Product();
                $product->setName($supply['name']);
                $product->setQuantity($supply['quantity']);
                $product->setPrice($supply['price']);
                $product->setUpc($supply['upc']);
                $product->setArticul($supply['articul']);
                $product->setSync(false);
                $this->em->persist($product);
                $products[] = $product;
            }
            else
            {
                return 0;
            }
        }

        if($files)
        {
            $raw = '';
            foreach ($files as $file)
            {
                if ($file instanceof UploadedFile)
                {
                    $raw .= file_get_contents($file->getPathname());

                }
            }
            $supplyFromCsv = $this->csvToArray($raw);
            unset($supplyFromCsv[0]);
            foreach ($supplyFromCsv as $productFromCsv)
            {
                if(isset($productFromCsv[0]))
                {
                    var_dump($productFromCsv[0]);
                    $NProduct = new Product();
                    $NProduct->setName($productFromCsv[2]);
                    $NProduct->setQuantity($productFromCsv[3]);
                    $NProduct->setArticul($productFromCsv[0]);
                    $NProduct->setUpc($productFromCsv[1]);
                    $NProduct->setDescription($productFromCsv[9]);
                    $NProduct->setPrice(324);
                    $this->em->persist($NProduct);
                    $products[] = $NProduct;
                }
            }
        }
        if(!empty($products))
        {
            $supply = new Supply();
            $supply->setSync(false);
            $supply->setSender($header['sender']);
            $supply->setRecipient($header['recipient']);
            $supply->setDate(new \DateTime());
            $supply->setContract($header['contract']);
            foreach ($products as $product)
            {
                $supply->addProduct($product);
            }
            $this->em->persist($supply);
            $this->em->flush();
        }
        return 1;
    }

    public function syncFromEbay()
    {
        $response = $this->ebay->getProductsFromEbay();

        if($response)
        {
            if(isset($response['ActiveList']))
            {
                $productsFromEbay = $response['ActiveList']['ItemArray']['Item'];
                foreach ($productsFromEbay as $product)
                {
                    /** @var Product $inProduct */
                    $inProduct = $this->em->getRepository(Product::class)->findOneBy(['eId' => $product['ItemID']]);
                    if(!$inProduct)
                    {
                        $inProduct = new Product();
                    }
                    if(isset($product['PictureDetails']))
                    {
                        $inProduct->setImg($product['PictureDetails']['GalleryURL']);
                    }
                    $inProduct->setName($product['Title']);
                    $inProduct->setUrl($product['ListingDetails']['ViewItemURL']);
                    $inProduct->setSync(true);
                    $inProduct->setQuantity($product['QuantityAvailable']);
                    $inProduct->setEId($product['ItemID']);
                    if(isset($product['BuyItNowPrice']))
                    {
                        $inProduct->setPrice($product['BuyItNowPrice']);
                    }
                    else
                    {
                        $inProduct->setPrice($product['StartPrice']);
                    }
                    $this->em->persist($inProduct);
                    $this->em->flush();
                }
            }
        }

    }

    public function deleteProducts($products)
    {
        foreach ($products as $product)
        {
            $this->em->remove($product);
        }
        $this->em->flush();
    }

    public function csvToArray($csv)
    {
        $lines = explode("\n", $csv);
        $data = array();
        foreach ($lines as $line) {

            $row = array();

            foreach (str_getcsv($line) as $field)
                $row[] = $field;

            $row = array_filter($row);

            $data[] = $row;
        }
        return $data;
    }

    public function syncToEbay($products)
    {
        foreach ($products as $product)
        {
            $data['title'] = $product->getName();
            $data['description'] = $product->getDescription();
            $data['quantity'] = $product->getQuantity();
            $data['upc'] = $product->getUpc();
            $data['price'] = $product->getPrice();
            $result = $this->ebay->addItem($data);
            if($result['Ack'] == 'Failure')
            {
                foreach($result['Errors'] as $error)
                {
                    var_dump($error['LongMessage']);
                }
            }
            if(isset($result['ItemID']))
            {

                    $product->seteId($result['ItemID']);
                    $product->setSync(true);
                    $this->em->persist($product);

            }
        }
    }

    public function syncOrdersFromEbay()
    {
        $response = $this->ebay->getOrdersFromEbay();
        $orders = $response['OrderArray']['Order'];

        foreach ($orders as $order)
        {
            $inOrder = $this->em->getRepository(Order::class)->findOneBy(['eId' => $order['OrderID']]);
            $product = $this->em->getRepository(Product::class)->findOneBy(['eId' => $order['TransactionArray']['Transaction']['Item']['ItemID']]);
            if(!$inOrder)
            {
                $inOrder = new Order();
            }
            $date = new \DateTime($order['CreatedTime']);
            $inOrder->setDate($date);
            $inOrder->setBuyer($order['TransactionArray']['Transaction']['Buyer']['Email']);
            $inOrder->setPrice($order['TransactionArray']['Transaction']['TransactionPrice']);
            if($product)
            {
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
        $sale->setPurchase($data['purchase']);
        $sale->setOrderNumber($data['orderNumber']);
        $sale->setStatus('sold');
        $sale->setPlatform($data['platform']);
        $product->setQuantity($product->getQuantity() - $data['quantity']);
        $this->em->persist($sale);
        $this->em->persist($product);
        $this->em->flush();
    }

    public function addKitProduct($products, $options)
    {
        $newProduct = new Product();
        $name = '';
        if($products)
        {
            foreach ($products as $product)
            {
                $newProduct->addKitProduct($product);
                $name.= $product->getName().' + ';
            }
            $name = mb_strrchr($name, ' + ', true);
            $newProduct->setArticul($options['article']);
            $newProduct->setPrice($options['price']);
            $newProduct->setQuantity($options['quantity']);
            $newProduct->setName($name);
            $newProduct->setKit(true);
            $this->em->persist($newProduct);
            $this->em->flush();
        }
    }
}