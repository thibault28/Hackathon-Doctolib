<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->passwordEncoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {


        // Création d’un utilisateur de type “auteur”
        $patient = new User();
        $patient->setEmail('patient@patient.fr');
        $patient->setFirstname('patient');
        $patient->setLastname('patient');
        $patient->setEmail('patient@patient.fr');
        $patient->setRoles(['ROLE_USER']);
        $patient->setPassword($this->passwordEncoder->encodePassword(
            $patient,
            'patient'
        ));
        
        $patient->setStatut($this->getReference('patient'));

        $manager->persist($patient);

        // Création d’un utilisateur de type “auteur”
        $medecin = new User();
        $medecin->setEmail('medecin@medecin.fr');
        $medecin->setFirstname('medecin');
        $medecin->setLastname('medecin');
        $medecin->setRoles(['ROLE_MEDECIN']);
        $medecin->setPassword($this->passwordEncoder->encodePassword(
            $medecin,
            'medecin'
        ));
        
        $medecin->setStatut($this->getReference('medecin'));

        $manager->persist($medecin);

        // Création d’un utilisateur de type “administrateur”
        $admin = new User();
        $admin->setEmail('admin@admin.fr');
        $admin->setFirstname('admin');
        $admin->setLastname('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            'admin'
        ));

        $manager->persist($admin);
        // Sauvegarde des 2 nouveaux utilisateurs :

        $manager->flush();
    }
}
