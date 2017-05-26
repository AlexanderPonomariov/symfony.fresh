<?php

namespace Fresh\CalcBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Fresh\CalcBundle\Entity\WorkTypes;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class WorkTypesFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $workType1 = new WorkTypes();
        $workType1->setId(1);
        $workType1->setWorkType('Дизайн');
        $manager->persist($workType1);

        $workType2 = new WorkTypes();
        $workType2->setId(2);
        $workType2->setWorkType('Программирование');
        $manager->persist($workType2);

        $workType3 = new WorkTypes();
        $workType3->setId(3);
        $workType3->setWorkType('Адаптив');
        $manager->persist($workType3);

        $workType4 = new WorkTypes();
        $workType4->setId(4);
        $workType4->setWorkType('Сложность');
        $manager->persist($workType4);

        $manager->flush();

        $this->addReference('Design', $workType1);
        $this->addReference('Programing', $workType2);
        $this->addReference('Adaptive', $workType3);
        $this->addReference('Complication', $workType4);
    }

    public function getOrder()
    {
        return 1;
    }

}