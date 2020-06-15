<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }

    public function load(ObjectManager $manager)
    {
        $faker  =  Faker\Factory::create('en_US');
        // TODO: Implement load() method.
        for ($i = 0; $i < 50; $i++ ) {
            $actor = new Actor();
            $actor->setName($faker->name);
            $actor->addProgram($this->getReference("program_" . $faker->numberBetween(0,5)));
            $manager->persist($actor);
            $this->addReference('actor_' . $i, $actor);
        }
        $manager->flush();
    }

}