<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Produit;

class ProduitFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $produit1 = new Produit();
        $produit1->setName("Strawberry")
            ->setImage("product-img-1.jpg")
            ->setPrice(85)
            ->setCategory("Fruits");

        $produit2 = new Produit();
        $produit2->setName("Berry")
            ->setImage("product-img-2.jpg")
            ->setPrice(70)
            ->setCategory("Fruits");


        $produit3 = new Produit();
        $produit3->setName("Lemon")
            ->setImage("product-img-3.jpg")
            ->setPrice(35)
            ->setCategory("Fruits");

        $manager->persist($produit1);
        $manager->persist($produit2);
        $manager->persist($produit3);

        $manager->flush();
    }
}
