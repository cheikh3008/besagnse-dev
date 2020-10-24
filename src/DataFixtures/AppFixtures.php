<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture

{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $role = new Role;
        $role->setLibelle('ROLE_ADMIN');
        $manager->persist($role);
        $user = new User;
        $user->setUsername('admin');
        $user->setPrenom('bee');
        $user->setNom('digital');
        $user->setTelephone('773043248');
        $user->setUsername('admin');
        $user->setRole($role);
        $user->setNomEntreprise('Bee Digital');
        $user->setAdresse('Sacre Coeur 2');
        $password = $this->encoder->encodePassword($user, 'admin123');
        $user->setPassword($password);

        $manager->persist($user);

        $manager->flush();
    }
}