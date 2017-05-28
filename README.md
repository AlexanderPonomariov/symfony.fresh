1) Clone repository with command:
    git clone https://github.com/AlexanderPonomariov/symfony.fresh.git
2) Run the following command in console:
    composer update
3) Set Data Base connection parameters
4) Run the following command in console for crating data base which was wrote while updating:
    php bin/console doctrine:database:create
5) Run the following command in console for creating data base tables, which was wrote in entities:
    php bin/console doctrine:schema:update --force
6) Create new migration:
    php bin/console doctrine:migrations:generate
7) Go to your_directory/app/DoctrineMigrations/VersionYYYYMMDDHHMMSS.php
8) Copy the following text inside function up():

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
            
9) Fill the base by default data with command:
    php bin/console doctrine:migrations:migrate