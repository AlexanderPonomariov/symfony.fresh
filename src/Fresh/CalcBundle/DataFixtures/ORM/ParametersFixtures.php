<?php
/**
 * Created by PhpStorm.
 * User: SanIK
 * Date: 23.05.2017
 * Time: 11:09
 */

namespace Fresh\CalcBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Fresh\CalcBundle\Entity\SitesTypes;

class ParametersFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $sitesTypes1 = new SitesTypes();
        $sitesTypes1->setSiteType('Магазин');
        $manager->persist($sitesTypes1);

        $sitesTypes1 = new SitesTypes();
        $sitesTypes1->setSiteType('Каталог');
        $manager->persist($sitesTypes1);

        $sitesTypes1 = new SitesTypes();
        $sitesTypes1->setSiteType('Лендинг');
        $manager->persist($sitesTypes1);

        $sitesTypes1 = new SitesTypes();
        $sitesTypes1->setSiteType('Самолет');
        $manager->persist($sitesTypes1);

        $manager->flush();
    }

}