<?php
// src/Blogger/BlogBundle/DataFixtures/ORM/BlogFixtures.php

namespace Fresh\CalcBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Fresh\CalcBundle\Entity\Parameters;
use Fresh\CalcBundle\Entity\WorkTypes;
use Fresh\CalcBundle\Entity\SitesTypes;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class ParametersFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $sitesTypes1 = new Parameters();
        $sitesTypes1->setParameterName('Главная');
        $sitesTypes1->setParameterValue('15');
        $sitesTypes1->setActive('1');
        $sitesTypes1->setUpdated(new \DateTime());
        $sitesTypes1->setSiteType($manager->merge($this->getReference('Shop')));
        $sitesTypes1->setParameterType($manager->merge($this->getReference('Design')));
        $manager->persist($sitesTypes1);

        $sitesTypes2 = new Parameters();
        $sitesTypes2->setParameterName('Главная');
        $sitesTypes2->setParameterValue('10');
        $sitesTypes2->setActive('1');
        $sitesTypes2->setUpdated(new \DateTime());
        $sitesTypes2->setSiteType($manager->merge($this->getReference('Catalogue')));
        $sitesTypes2->setParameterType($manager->merge($this->getReference('Design')));
        $manager->persist($sitesTypes2);

        $sitesTypes3 = new Parameters();
        $sitesTypes3->setParameterName('Главная');
        $sitesTypes3->setParameterValue('25');
        $sitesTypes3->setActive('1');
        $sitesTypes3->setUpdated(new \DateTime());
        $sitesTypes3->setSiteType($manager->merge($this->getReference('Landing')));
        $sitesTypes3->setParameterType($manager->merge($this->getReference('Design')));
        $manager->persist($sitesTypes3);

        $sitesTypes4 = new Parameters();
        $sitesTypes4->setParameterName('Главная');
        $sitesTypes4->setParameterValue('10');
        $sitesTypes4->setActive('1');
        $sitesTypes4->setUpdated(new \DateTime());
        $sitesTypes4->setSiteType($manager->merge($this->getReference('Plain')));
        $sitesTypes4->setParameterType($manager->merge($this->getReference('Design')));
        $manager->persist($sitesTypes4);

        $sitesTypes5 = new Parameters();
        $sitesTypes5->setParameterName('Корзина');
        $sitesTypes5->setParameterValue('10');
        $sitesTypes5->setActive('1');
        $sitesTypes5->setUpdated(new \DateTime());
        $sitesTypes5->setSiteType($manager->merge($this->getReference('Shop')));
        $sitesTypes5->setParameterType($manager->merge($this->getReference('Design')));
        $manager->persist($sitesTypes5);

        $sitesTypes6 = new Parameters();
        $sitesTypes6->setParameterName('Галерея');
        $sitesTypes6->setParameterValue('10');
        $sitesTypes6->setActive('1');
        $sitesTypes6->setUpdated(new \DateTime());
        $sitesTypes6->setSiteType($manager->merge($this->getReference('Plain')));
        $sitesTypes6->setParameterType($manager->merge($this->getReference('Design')));
        $manager->persist($sitesTypes6);


        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }

}