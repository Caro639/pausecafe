<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $parent = $this->createCategory('Café', null, $manager);

        $this->createCategory('Robusta', $parent, $manager);
        $this->createCategory('Arabica', $parent, $manager);
        $this->createCategory('Mexican', $parent, $manager);
        $this->createCategory('African', $parent, $manager);
        $this->createCategory('Colombian', $parent, $manager);

        $parent = $this->createCategory('Populaires', null, $manager);
        $this->createCategory('Espresso', $parent, $manager);
        $this->createCategory('Cappuccino', $parent, $manager);
        $this->createCategory('Latte', $parent, $manager);

        $parent = $this->createCategory('Nouveautés', null, $manager);
        $this->createCategory('Drip', $parent, $manager);
        $this->createCategory('Filter', $parent, $manager);
        $this->createCategory('Turkish', $parent, $manager);

        $manager->flush();
    }

    public function createCategory(string $name, ?Categories $parent = null, ObjectManager $manager): Categories
    {
        $category = new Categories();
        $category->setName($name);
        $category->setSlug($this->slugger->slug($category->getName())->lower());
        if ($parent) {
            $category->setParent($parent);
        }
        $manager->persist($category);

        return $category;
    }
}
