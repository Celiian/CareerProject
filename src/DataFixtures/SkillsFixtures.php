<?php
// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use App\Entity\Skills;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SkillsFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $skill = new Skills();
        $skill->setName('Html');
        $manager->persist($skill);
        $skill = new Skills();
        $skill->setName('Css');
        $manager->persist($skill);
        $skill = new Skills();
        $skill->setName('Java');
        $manager->persist($skill);
        $skill = new Skills();
        $skill->setName('Python');
        $manager->persist($skill);
        $skill = new Skills();
        $skill->setName('Php');
        $manager->persist($skill);
        $skill = new Skills();
        $skill->setName('C++');
        $manager->persist($skill);
        $skill = new Skills();
        $skill->setName('C');
        $manager->persist($skill);
        $skill = new Skills();
        $skill->setName('C#');
        $manager->persist($skill);
        $skill = new Skills();
        $skill->setName('Ruby');
        $manager->persist($skill);
        $skill = new Skills();
        $skill->setName('R');
        $manager->persist($skill);
        $skill = new Skills();
        $skill->setName('Swift');
        $manager->persist($skill);
        $skill = new Skills();
        $skill->setName('Scala');
        $manager->persist($skill);

        $manager->flush();
    }
}