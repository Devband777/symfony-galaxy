<?php
// src/Command/GenerateFakeDataCommand.php
namespace App\Command;

use App\Entity\Atom;
use App\Entity\Galaxy;
use App\Entity\Star;
use Doctrine\ORM\EntityManagerInterface;
// use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateFakeDataCommand extends Command
{
    protected static $defaultName = 'app:generate-fake-data';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Generates fake data for galaxies, stars, and atoms.')
            ->setHelp('This command generates fake data for galaxies, stars, and atoms.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $faker = Factory::create();

        // Clear any existing data from the database
        $this->entityManager->createQuery('DELETE FROM App\Entity\Atom')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Star')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Galaxy')->execute();

        // Generate atoms
        $atoms = [];
        for ($i = 1; $i <= 172; $i++) {
            $atom = new Atom();
            $atom->setNumber($i);
            $this->entityManager->persist($atom);
            $atoms[] = $atom;
        }

        // Generate galaxies
        $galaxies = [];
        for ($i = 1; $i <= 5; $i++) {
            $galaxy = new Galaxy();
            $galaxy->setName("Galaxy ".$i);
            shuffle($atoms);
            $galaxy->setAtoms(array_slice($atoms, 0, 50));
            $this->entityManager->persist($galaxy);
            $galaxies[] = $galaxy;
        }

        // Generate stars
        foreach ($galaxies as $i => $galaxy) {
            for ($j = 1; $j <= 1000; $j++) {
                $star = new Star();
                $star->setName("Star ".$j);
                $star->setGalaxy($galaxy);
                $star->setRadius(rand(500, 1000));
                $star->setTemperature(rand(100, 1000));
                $star->setRotationFrequency(rand(1, 5));
                $temp_array = [];
                $temp_array = $galaxies[$i]->getAtoms();
                // var_dump(is_array($temp_array));
                shuffle($temp_array);
                $star->setAtoms(array_slice($temp_array, 0, 10));
                $this->entityManager->persist($star);
            }
        }

        $this->entityManager->flush();

        $output->writeln('Fake data generated successfully.');

        return 0;
    }
}