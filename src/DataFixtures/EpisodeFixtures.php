<?php


namespace App\DataFixtures;


use App\Entity\Episode;
use App\Services\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{

    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }

    public function load(ObjectManager $manager)
    {

        $faker = Faker\Factory::create('fr_FR');
        $slugify = new Slugify();

        for ($i = 0; $i < 5; $i++) {
            for ($ii = 0; $ii < 10; $ii++) {
                for ($j = 0; $j < 10; $j++) {
                    $episode = new Episode();
                    $episode->setTitle($faker->sentence);
                    $episode->setSlug($slugify->generate($episode->getTitle()));
                    $episode->setSynopsis($faker->paragraph);
                    $episode->setNumber($j);
                    $episode->setSeason($this->getReference('program_' . $i . 'season_' . $ii));
                    $manager->persist($episode);
                }
            }
        }
        $manager->flush();
    }

}