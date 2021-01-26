<?php

namespace App\Command;

use App\Add\Add;
use App\Entity\Description;
use App\Entity\Product;
use App\Entity\ProductGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TestCommand extends Command
{
    protected static $defaultName = 'test';
    private $add;
    private $em;
    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    public function __construct(Add $add, EntityManagerInterface $em, string $name = null)
    {
        parent::__construct($name);
        $this->add = $add;
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');


        $file = file_get_contents('/Users/sergey/Downloads/groups.csv');

        $array = $this->add->csvToArray($file);

        foreach ($array as $item)
        {
            $article = $item['article'];
            $groupName = $item['group'];
            $group = $this->em->getRepository(Description::class)->findOneByPlName($groupName);
            if(!$group)
            {
                $group = new Description();
                $group
                    ->setPlName($groupName);
                $this->em->persist($group);
            }

            $product = $this->em->getRepository(Product::class)->findOneBy(['articul' => $article]);

            $product->setDes($group);
            $this->em->flush();
        }
        return Command::SUCCESS;
    }
}
