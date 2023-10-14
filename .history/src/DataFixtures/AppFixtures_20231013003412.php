<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i=0; $i < 20; $i++) { 
            $book = new Book();
            $book -> setTitle("Livre ".$i);
            $book -> setCoverText("Quatriéme du numéro : "$i)
        }

        $manager->flush();
    }
}
