These instructions will run project on your local machine for development

### Prerequisites

- version of [PHP](http://php.net/manual/en/intro-whatis.php): 7+

Configure `.env.local` in root folder with secrets for running app

### Installing

    composer install
    ./bin/console doctrine:database:create
    ./bin/console doctrine:schema:update --force
    ./bin/console hautelook:fixtures:load
    ./bin/console app:update-exchange-rate 
    ./bin/console server:run
    
Go to [http://127.0.0.1:8000/](http://127.0.0.1:8000/)