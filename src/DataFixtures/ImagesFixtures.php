<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Images;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ImagesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $usedNames = []; // Tableau pour stocker les noms déjà utilisés

        for ($img = 1; $img <= 100; $img++) {
            $image = new Images();
            // $image->setName($faker->image(null, 640, 480));

            // Générer un nom unique
            do {
                $name = $faker->unique()->word . '_' . uniqid() . '.jpg';
            } while (in_array($name, $usedNames));

            $usedNames[] = $name; // Ajouter le nom au tableau des noms utilisés
            $image->setName($name);
            $product = $this->getReference('prod-' . rand(1, 10), \App\Entity\Products::class);
            // $image->setProducts($product);
            // if ($product instanceof \App\Entity\Products) {
            //     $image->setName($faker->imageUrl(640, 480));
            // }
            if ($product instanceof \App\Entity\Products) {
                $image->setProducts($product);
            }
            $manager->persist($image);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProductsFixtures::class,
        ];
    }
}
