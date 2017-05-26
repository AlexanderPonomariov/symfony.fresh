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
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class SitesTypesFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $sitesTypes1 = new SitesTypes();
        $sitesTypes1->setId(1);
        $sitesTypes1->setSiteType('Магазин');
        $manager->persist($sitesTypes1);

        $sitesTypes2 = new SitesTypes();
        $sitesTypes2->setId(2);
        $sitesTypes2->setSiteType('Каталог');
        $manager->persist($sitesTypes2);

        $sitesTypes3 = new SitesTypes();
        $sitesTypes3->setId(3);
        $sitesTypes3->setSiteType('Лендинг');
        $manager->persist($sitesTypes3);

        $sitesTypes4 = new SitesTypes();
        $sitesTypes4->setId(4);
        $sitesTypes4->setSiteType('Самолет');
        $manager->persist($sitesTypes4);

        $manager->flush();

        $this->addReference('Shop', $sitesTypes1);
        $this->addReference('Catalogue', $sitesTypes2);
        $this->addReference('Landing', $sitesTypes3);
        $this->addReference('Plain', $sitesTypes4);
    }

    public function getOrder()
    {
        return 1;
    }

}