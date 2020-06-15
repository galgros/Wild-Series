<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 5; $i++) {
            for ($ii = 0; $ii < 10; $ii++) {
                $season = new Season();
                $season->setDescription($faker->paragraph);
                $season->setYear($faker->year);
                $season->setProgram($this->getReference('program_' . $i));
                $season->setNumber($ii + 1);
                $manager->persist($season);
                $this->addReference('program_' . $i . 'season_' . $ii, $season);
            }
        }

        $manager->flush();
    }
}