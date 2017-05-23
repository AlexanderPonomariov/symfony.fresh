<?php
// src/Blogger/BlogBundle/DataFixtures/ORM/BlogFixtures.php

namespace Fresh\CalcBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Fresh\CalcBundle\Entity\SitesTypes;

class SitesTypesFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $sitesTypes1 = new SitesTypes();
        $sitesTypes1->setSiteType('Магазин');
        $manager->persist($sitesTypes1);

        $sitesTypes2 = new SitesTypes();
        $sitesTypes2->setSiteType('Каталог');
        $manager->persist($sitesTypes2);

        $sitesTypes3 = new SitesTypes();
        $sitesTypes3->setSiteType('Лендинг');
        $manager->persist($sitesTypes3);

        $sitesTypes4 = new SitesTypes();
        $sitesTypes4->setSiteType('Самолет');
        $manager->persist($sitesTypes4);

        $manager->flush();
    }

}