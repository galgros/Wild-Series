<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    const CATEGORY = [
        'Science Fiction',
        'Action',
        'Comedie',
        'Horreur',
        'Drame'
    ];

    public function load(ObjectManager $manager)
    {
        $i = 0;
        // TODO: Implement load() method.
        foreach (self::CATEGORY as $name) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
            $this->addReference('categorie_' . $i, $category);
            $i++;
        }

        $manager->flush();
    }
}