<?php


namespace App\Add;


use App\Entity\OrderAllegroOffers;
use App\Entity\OutOfStock;
use App\Entity\Product;
use App\Entity\Sale;
use App\Entity\SupplyProduct;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class Statistic
{
    private $productRep;
    private $supplyRep;
    private $saleRep;
    private $em;
    private $ofsRep;
    private $orderAllegroOffersRep;

    public function __construct(EntityManagerInterface $em)
    {
        $this->productRep = $em->getRepository(Product::class);
        $this->supplyRep = $em->getRepository(SupplyProduct::class);
        $this->saleRep = $em->getRepository(Sale::class);
        $this->orderAllegroOffersRep = $em->getRepository(OrderAllegroOffers::class);
        $this->ofsRep = $em->getRepository(OutOfStock::class);
        $this->em = $em;
    }

    public function getSaleSpeedByDate(DateTime $startDate, DateTime $endDate = null)
    {
        $positions = [];
        if (!$endDate) {
            $endDate = new DateTime('now');
        }
        $dateDiff = strtotime($endDate->format('Y-m-d')) - strtotime($startDate->format('Y-m-d'));
        /** @var Product[] $products */
        $products = $this->productRep->findWithSales();
        foreach ($products as $product) {
            $sumSales = 0;
            foreach ($product->getAllegroOffers() as $allegroOffer) {
                $sumSales += $this->getSumQuantity($this->orderAllegroOffersRep->findByDate($startDate, $endDate, $allegroOffer));
            }
            $positions[] = [
                'product' => $product,
                'speed' => $sumSales/floor($dateDiff / (60 * 60 * 24)),
                'quantity' => $product->getQuantity(),
            ];
        }
        return $positions;
    }

    public function getSumQuantity($orderAllegroOffers): ?int
    {
        $result = 0;
        foreach ($orderAllegroOffers as $order) {
            $result += $order->getQuantity();
        }
        return $result;
    }

    public function outOfStockDays(Product $product, DateTime $startDate, DateTime $endDate = null)
    {
        if(!$endDate) {
            $endDate = new DateTime('now');
        }
        return $this->ofsRep->getOutOfStockDays($product, $startDate, $endDate);
    }

    public function getSaleStatistic($sales)
    {
        return [
            'total' => count($sales),
            'sum' => $this->getSumProducts($sales)
        ];
    }

    /**
     * @param Sale[] $sales
     * @return int
     */
    public function getSumProducts($sales): int
    {
        $sum = 0;
        foreach ($sales as $sale) {
            foreach ($sale->getOrder()->getOrderAllegroOffers() as $allegroOffer) {
                $sum += $allegroOffer->getAllegroOffer()->getProduct()->getPrice();
            }
        }
        return $sum;
    }
}