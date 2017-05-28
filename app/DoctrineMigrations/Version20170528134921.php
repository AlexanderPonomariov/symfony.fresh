<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170528134921 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('INSERT INTO sites_types (`id`, `site_type`) VALUES (1,\'Магазин\'),(2,\'Лендинг\'),(3,\'Каталог\'),(4,\'Самолет\')');
        $this->addSql('INSERT INTO work_types (`id`, `work_type`) VALUES (1,\'Дизайн\'),(2,\'Программирование\'),(3,\'Адаптив\'),(4,\'Сложность\')');
        $this->addSql('INSERT INTO parameters (`id`, `parameter_name`,`parameter_value`,`active`,`updated`,`site_type_id`,`work_type_id`) VALUES 
                (1,\'Главная\',\'28\',\'1\',\'\',\'1\',\'1\'),
                (2,\'Главная\',\'15\',\'1\',\'\',\'2\',\'1\'),
                (3,\'Главная\',\'25\',\'1\',\'\',\'3\',\'1\'),
                (4,\'Главная\',\'15\',\'1\',\'\',\'4\',\'1\'),
                (5,\'Корзина\',\'10\',\'1\',\'\',\'1\',\'1\'),                
                (6,\'Фильтры\',\'5\',\'1\',\'\',\'1\',\'2\'),
                (7,\'Новая Почта\',\'7\',\'1\',\'\',\'1\',\'2\'),
                
                (8,\'Сложность\',\'10\',\'1\',\'\',\'1\',\'4\'),
                (9,\'Адаптив\',\'40\',\'1\',\'\',\'1\',\'3\'),
                (10,\'Сложность\',\'10\',\'1\',\'\',\'2\',\'4\'),
                (11,\'Адаптив\',\'40\',\'1\',\'\',\'2\',\'3\'),
                (12,\'Сложность\',\'10\',\'1\',\'\',\'3\',\'4\'),
                (13,\'Адаптив\',\'40\',\'1\',\'\',\'3\',\'3\')
        ');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
