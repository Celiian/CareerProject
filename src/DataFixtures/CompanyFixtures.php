<?php
// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CompanyFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $company = new Company("Youtube", "youtube@gmail.com", "YouTube est un site web d'hébergement de vidéos et média social sur lequel les utilisateurs peuvent envoyer, regarder, commenter, évaluer et partager des vidéos en streaming. ");
        $manager->persist($company);
        $company = new Company("Google", "google@gmail.com", "Google LLC /ˈguːgəl/ est une entreprise américaine de services technologiques fondée en 1998 dans la Silicon Valley, en Californie, par Larry Page et Sergey Brin, créateurs du moteur de recherche Google. C'est une filiale de la société Alphabet depuis août 2015.");
        $manager->persist($company);
        $company = new Company("Apple", "apple@gmail.com", "Apple [ˈæpəl] est une entreprise multinationale américaine qui crée et commercialise des produits électroniques grand public, des ordinateurs personnels et des logiciels");
        $manager->persist($company);

        $manager->flush();
    }
}