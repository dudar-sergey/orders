<?php

namespace App\Command;

use App\ebay\AllegroUserManager;
use App\Entity\CronLog;
use App\Entity\Profile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\JsonResponse;

class CronCommand extends Command
{
    protected static $defaultName = 'cron';
    protected static $defaultDescription = 'Add a short description for your command';
    private $em;
    private $authAllegro;
    private $profileRep;
    private $am;

    public function __construct(EntityManagerInterface $em, AllegroUserManager $am, string $name = null)
    {
        parent::__construct($name);
        $this->em = $em;
        $this->profileRep = $em->getRepository(Profile::class);
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
        $cronLog = new CronLog();
        $cronLog->setText('Выполнен');
        $cronLog->setCreateAt(new \DateTime('now', new \DateTimeZone('Europe/Moscow')));
        $this->em->persist($cronLog);
        $this->em->flush();
        /** @var Profile[] $profiles */
        $profiles = $this->profileRep->findAllAllegro();
        foreach ($profiles as $profile) {
            try {
                $tokens = $this->am->getTokenForUserWithRefreshToken($profile);
                $profile->setAllegroAccessToken($tokens['accessToken']);
                $profile->setAllegroRefreshToken($tokens['refreshToken']);
                $this->em->flush();
            } catch (\Exception $e) {}
        }
        return Command::SUCCESS;
    }
}
