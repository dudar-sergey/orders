<?php


namespace App\Add;


use App\ebay\AllegroUserManager;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Progress;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Manager
{
    protected $orderRep;
    protected $productRep;
    protected $em;
    protected $am;
    protected $progressRep;
    protected $client;

    public function __construct(EntityManagerInterface $em, AllegroUserManager $am)
    {
        $this->em = $em;
        $this->am = $am;
        $this->orderRep = $em->getRepository(Order::class);
        $this->productRep = $em->getRepository(Product::class);
        $this->progressRep = $em->getRepository(Progress::class);
        $this->client = HttpClient::create();
    }

    /**
     * @param $message - сообщение
     */
    protected function log($message)
    {
        $nowTime = new DateTime('now', new DateTimeZone('+3'));
        $string = '['.$nowTime->format('d-m-Y G:i:s').'] '.$message;
        file_put_contents(__DIR__.'/logs.txt', file_get_contents(__DIR__.'/logs.txt').PHP_EOL.$string);
    }

    public function send($url)
    {
        try {
            return $this->client->request('GET', $url)->getContent();
        }catch (\Exception | TransportExceptionInterface | ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface $e) {
            return false;
        }
    }
}