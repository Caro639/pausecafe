<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Products;
use App\DataFixtures\CategoriesFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductsFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($prod = 1; $prod <= 10; $prod++) {
            $product = new Products();
            $product->setName($faker->text(15));
            $product->setDescription($faker->text(200));
            $product->setSlug($this->slugger->slug($product->getName())->lower());
            $product->setPrice($faker->numberBetween(400, 1500));
            $product->setStock($faker->numberBetween(0, 100));

            $categories = $this->getReference('cat-' . rand(1, 13), \App\Entity\Categories::class);
            if ($categories instanceof \App\Entity\Categories) {
                $product->setCategories($categories);
            }

            $this->setReference('prod-' . $prod, $product);
            $manager->persist($product);
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
