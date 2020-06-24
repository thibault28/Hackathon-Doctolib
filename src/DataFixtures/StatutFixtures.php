<?php

namespace App\DataFixtures;

use App\Entity\Statut;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class StatutFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $patient = new Statut();

        $patient->setName('patient');
        $this->addReference('patient',$patient);
        $manager->persist($patient);

        $medecin = new Statut();

        $medecin->setName('medecin');
        $this->addReference('medecin',$medecin);
        $manager->persist($medecin);


        
        $manager->flush();
    }
}
