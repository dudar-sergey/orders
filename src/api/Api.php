<?php


namespace App\api;


use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class Api
{
    private $em;
    private $serializer;


    public function __construct(EntityManagerInterface $em)
    {
        $encoders = new JsonEncoder();
        $normalizers = new ObjectNormalizer();
        $serializer = new Serializer([$normalizers], [$encoders]);
        $this->serializer = $serializer;
        $this->em = $em;
    }

    protected function response($data, $status=200, $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }

    public function getOrders()
    {
        /** @var Order $orders */
        $orders = $this->em->getRepository(Order::class)->findAll();
        if($orders)
        {
            foreach ($orders as $order)
            {
                $date = $order->getDate()->format('d-m-Y');
                $data['orders'][] = [
                    'allegroId' => $order->getAllegroId(),
                    'allegroOffer' => $order->getAllegroOffer(),
                    'buyer' => $order->getBuyer(),
                    'eId' => $order->getEId(),
                    'payment' => $order->getPayment(),
                    'placement' => $order->getPlacement(),
                    'price' => $order->getPrice(),
                    'products' => $order->getProducts(),
                    'id' => $order->getId(),
                    'date' => $date,
                ];
            }
            $orders = $this->transformInArray($orders);
            foreach ($orders[0] as $key => $order)
            {
                $data['columns'][] = [
                    'field' => $key,
                    'label' => $key,
                    'centered' => true,
                    'searchable' => true

                ];
            }
            return $this->response($data);
        }
        else
        {
            $data = [
                'errors' => [
                    'Ошибка получения данных',
                ],
                'status' => 400,
            ];
            return $this->response($data, 400);
        }
    }

    public function transformInArray($data)
    {
        $array = [];
        foreach ($data as $datum)
        {
            $array[] = json_decode($this->serializer->serialize($datum, 'json', [
                    'circular_reference_limit' => 2,
                    'circular_reference_handler' => function ($object) {
                        return $object->getId();
                    }
            ]));
        }

        return $array;
    }


    public function getProducts($filters = null)
    {
        $data = [];
        if($filters)
        {
           $wordSearch = $filters['wordSearch'];
           $products = $this->em->getRepository(Product::class)->findByArticleAndName($wordSearch);
        }
        else
        {
            $products = $this->em->getRepository(Product::class)->findAll();
        }
        if($products)
        {
            foreach ($products as $product)
            {
                $data[] = [
                    'name' => $product->getArticul().' '.$product->getName(),
                    'id' => $product->getId(),
                ];
            }
            return $this->response($data);
        }
        else
        {
            $data = [
                'errors' => [
                    'Ничего не найдено',
                ],
                'status' => 400,
            ];
            return $this->response($data, 400);
        }
    }
}