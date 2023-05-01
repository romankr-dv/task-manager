# task-manager
Just task managing and time traking app.  

Based on symfony docker template:  
https://github.com/dunglas/symfony-docker

## Setup
Initialize project:
```
composer install
npm install --force
```

Start dev environment:
```
npm run watch 
docker-compose up
```

Setup database:
```
docker exec -it task-manager-upgrade-php-1 bin/console doctrine:migrations:migrate
docker exec -it task-manager-upgrade-php-1 bin/console app:create-user test@email.com password
```
