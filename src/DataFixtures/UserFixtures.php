<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $chahine = new User();
        $chahine->setEmail("chahine@mail.com")
            ->setUsername("chahine")
            ->setPassword("chahine")
            ->setRole("Visiteur");

        $admin = new User();
        $admin->setEmail("admin@mail.com")
            ->setPassword("admin")
            ->setUsername("Admin")
            ->setRole("Admin");


        $manager->persist($chahine);
        $manager->persist($admin);
        $manager->flush();
    }
}
