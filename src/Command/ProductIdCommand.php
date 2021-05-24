<?php

namespace App\Command;

use App\ebay\AllegroUserManager;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProductIdCommand extends Command
{
    protected static $defaultName = 'productId';
    protected static $defaultDescription = 'Add a short description for your command';
    protected $session;
    protected $em;
    protected $am;

    public function __construct(AllegroUserManager $am, SessionInterface $session, EntityManagerInterface $em, string $name = null)
    {
        parent::__construct($name);
        $this->session = $session;
        $this->em = $em;
        $this->am = $am;
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');
        /** @var Product[] $products */
        $products = $this->em->getRepository(Product::class)->findAll();
        foreach ($products as $product) {
            $data = json_decode($this->am->getProductId($product->getUpc()), true);
            $product->setAllegroProductId($data['products'][0]['id']);
            $product->setAllegroImages($data['products'][0]['images']);
        }
        $this->em->flush();
        return Command::SUCCESS;
    }
}
