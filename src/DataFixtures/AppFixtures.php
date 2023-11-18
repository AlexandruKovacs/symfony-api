<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Factory\CategoryFactory;
use App\Factory\PostFactory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        CategoryFactory::createMany(8);
        PostFactory::createMany(40, function() {
            return [
                'category' => CategoryFactory::random(),
            ];
        });

        $manager->flush();
    }
}
