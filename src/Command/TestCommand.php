<?php

namespace App\Command;

use App\Add\Add;
use App\Add\Statistic;

use App\ebay\AllegroUserManager;
use App\Entity\AllegroOffer;
use App\Entity\Images;
use App\Entity\OrderAllegroOffers;
use App\Entity\Product;
use App\Entity\Profile;
use App\Entity\User;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TestCommand extends Command
{
    protected static $defaultName = 'test';
    private $add;
    private $em;
    private $client;
    private $statistic;
    private $am;
    private $pe;
    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    public function __construct(Statistic $statistic, AllegroUserManager $am, Add $add, EntityManagerInterface $em, string $name = null, UserPasswordEncoderInterface $pe)
    {
        parent::__construct($name);
        $this->add = $add;
        $this->em = $em;
        $this->statistic = $statistic;
        $this->am = $am;
        $this->pe = $pe;
        $this->client = HttpClient::create([
            //'proxy'=>'http://wNogF3:k1VdVC@185.183.161.196:8000',
        ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $articles = [];
        $arg1 = $input->getArgument('arg1');
        $orderAllegroOffers = $this->em->getRepository(OrderAllegroOffers::class)->findAll();
        foreach ($orderAllegroOffers as $orderAllegroOffer) {
            $offer = $orderAllegroOffer->getAllegroOffer();
            if($offer) {
                $product = $offer->getProduct();
                $orderAllegroOffer->setProduct($product);
            }
        }
        $this->em->flush();
        return Command::SUCCESS;
    }

    public function syncFromAllegro()
    {
        $profile = $this->em->getRepository(Profile::class)->find(6);
        $allegroOffers = $this->em->getRepository(AllegroOffer::class)->findBy(['profile' => 6]);
        $data = $this->am->getOffersFromAllegro($profile);
        foreach ($data['offers'] as $offer) {
            $product = $this->em->getRepository(Product::class)->findOneBy(['articul' => $offer['external']['id']]);
            if($product) {
                $allegroOffer = $product->getAllegroOffer($profile);
                if($allegroOffer) {
                    $allegroOffer->setProfile($profile);
                    if($offer['publication']['status'] != 'ACTIVE') {
                        $allegroOffer->setStatus(false);
                    } elseif ($offer['publication']['status'] == 'ACTIVE') {
                        $allegroOffer->setStatus(true);
                    }
                } else {
                    $allegroOffer = new AllegroOffer();
                    $allegroOffer->setProduct($product);
                    if($offer['publication']['status'] == 'ACTIVE') {
                        $allegroOffer->setStatus(true);
                    } else {
                        $allegroOffer->setStatus(false);
                    }
                    $allegroOffer->setProfile($profile);
                    $allegroOffer->setAllegroId($offer['id']);
                    $this->em->persist($allegroOffer);
                }
                $this->em->flush();
            }
        }
    }
}
